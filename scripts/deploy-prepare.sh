#!/usr/bin/env bash
#
# deploy-prepare.sh – Replace URL, Export DB, ZIP mã nguồn
# Chạy từ thư mục gốc dự án (parent của web/).
# Yêu cầu: DDEV đang chạy (cho replace-url và export-db).
#

set -e

# --- Cấu hình (sửa nếu cần) ---
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
PROJECT_ROOT="$(cd "$SCRIPT_DIR/.." && pwd)"
OUTPUT_DIR="${PROJECT_ROOT}/deploy-output"
DATE=$(date +%Y%m%d-%H%M%S)

# Thư mục chứa kết quả: deploy-output/YYYYMMDD-HHMMSS/
OUTPUT_SUBDIR="${OUTPUT_DIR}/${DATE}"

# Tên file DB export
DB_FILENAME="database-${DATE}.sql"

# Tên file ZIP (không có khoảng trắng)
ZIP_FILENAME="visaminhquan-src-${DATE}.zip"

# --- Hàm in màu ---
red='\033[0;31m'
green='\033[0;32m'
yellow='\033[1;33m'
nc='\033[0m'
info() { echo -e "${green}[INFO]${nc} $1"; }
warn() { echo -e "${yellow}[WARN]${nc} $1"; }
err()   { echo -e "${red}[ERROR]${nc} $1"; }

# --- Kiểm tra chạy từ đúng thư mục ---
cd "$PROJECT_ROOT"
if [[ ! -d "web" ]] || [[ ! -f "web/wp-config.php" ]]; then
  err "Chạy script từ thư mục gốc dự án (có chứa web/ và web/wp-config.php)."
  exit 1
fi

# --- Tạo thư mục output ---
mkdir -p "$OUTPUT_SUBDIR"

# --- 1. Replace URL trong toàn bộ database (GHI ĐÈ DB HIỆN TẠI – chỉ dùng khi cố ý đổi URL trên DEV) ---
replace_url() {
  local OLD_URL="$1"
  local NEW_URL="$2"
  if [[ -z "$OLD_URL" || -z "$NEW_URL" ]]; then
    err "Thiếu tham số. Cách dùng: $0 replace-url <OLD_URL> <NEW_URL>"
    echo "Ví dụ: $0 replace-url 'https://visaminhquan.ddev.site' 'https://visaminhquan.com.vn'"
    exit 1
  fi
  warn "replace-url sẽ thay đổi database hiện tại. Để chuẩn bị deploy mà không đụng DEV, dùng: $0 all <OLD> <NEW>"
  info "Replace URL trong database: $OLD_URL -> $NEW_URL"
  if ! ddev exec -d /var/www/html/web "wp search-replace \"$OLD_URL\" \"$NEW_URL\" --all-tables --report-changed-only"; then
    err "Replace URL thất bại. Đảm bảo DDEV đang chạy: ddev start"
    exit 1
  fi
  ddev exec -d /var/www/html/web "wp cache flush" 2>/dev/null || true
  info "Replace URL xong."
}

# --- 2. Export database ---
export_db() {
  local DEST="${OUTPUT_SUBDIR}/${DB_FILENAME}"
  info "Export database -> $DEST"
  if ! ddev export-db --file="$DEST"; then
    err "Export DB thất bại. Đảm bảo DDEV đang chạy."
    exit 1
  fi
  info "Export DB xong: $DEST"
}

# --- 3. ZIP mã nguồn (CHỈ thư mục web/, không bao gồm thư mục web bên ngoài) ---
zip_source() {
  local DEST="${OUTPUT_SUBDIR}/${ZIP_FILENAME}"
  info "ZIP mã nguồn -> $DEST"
  # Chỉ zip các thư mục / tệp BÊN TRONG thư mục web/
  # Loại trừ: .git, .ddev, node_modules, deploy-output, .DS_Store, __MACOSX, .env, *.log, file zip
  if command -v zip &>/dev/null; then
    (
      cd "$PROJECT_ROOT/web"
      zip -r "$DEST" . \
        -x "*.git*" \
        -x "*/.ddev/*" \
        -x "*/node_modules/*" \
        -x "*deploy-output*" \
        -x "*.DS_Store" \
        -x "*__MACOSX*" \
        -x "*.env" \
        -x "*.log" \
        -x "*.zip" 2>/dev/null || true
    )
    # Bỏ qua lỗi zip với một số path
    if [[ -f "$DEST" ]]; then
      info "ZIP xong: $DEST"
    else
      err "Tạo file ZIP thất bại."
      exit 1
    fi
  else
    warn "Lệnh 'zip' không tìm thấy. Thử dùng tar.gz."
    local TARNAME="visaminhquan-src-${DATE}.tar.gz"
    tar --exclude='.git' \
        --exclude='.ddev' \
        --exclude='node_modules' \
        --exclude='deploy-output' \
        --exclude='.DS_Store' \
        --exclude='*.log' \
        --exclude='*.zip' \
        -czf "${OUTPUT_SUBDIR}/${TARNAME}" -C "$PROJECT_ROOT/web" .
    info "Tạo archive xong: ${OUTPUT_SUBDIR}/${TARNAME}"
  fi
}

# --- 4. Export DB rồi replace URL trong file (không đổi DB hiện tại) ---
export_db_with_replace() {
  local OLD_URL="$1"
  local NEW_URL="$2"
  if [[ -z "$OLD_URL" || -z "$NEW_URL" ]]; then
    err "Thiếu tham số. Cách dùng: $0 export-db-replace <OLD_URL> <NEW_URL>"
    exit 1
  fi
  local DEST="${OUTPUT_SUBDIR}/${DB_FILENAME}"
  info "Export DB, sau đó replace URL trong file: $OLD_URL -> $NEW_URL (không đổi DB hiện tại)"
  # Export dạng plain SQL (--gzip=false) để sed thay URL được; file gzip sẽ là binary, sed báo lỗi.
  ddev export-db --gzip=false --file="$DEST"
  if [[ "$(uname)" = "Darwin" ]]; then
    sed -i '' "s|${OLD_URL}|${NEW_URL}|g" "$DEST"
  else
    sed -i "s|${OLD_URL}|${NEW_URL}|g" "$DEST"
  fi
  info "Export DB (đã replace URL trong file) xong: $DEST"
}

# --- 5. Tạo script CHMOD để chạy trên server sau khi upload ---
gen_chmod_script() {
  local DEST="${OUTPUT_SUBDIR}/chmod-on-server.sh"
  info "Tạo script CHMOD -> $DEST"
  cat > "$DEST" << 'CHMOD_EOF'
#!/usr/bin/env bash
# Chạy script này TRÊN SERVER, trong thư mục gốc WordPress (chứa wp-config.php).
# Cách dùng: upload file này lên server, rồi: chmod +x chmod-on-server.sh && ./chmod-on-server.sh

set -e
echo "[CHMOD] Đặt quyền thư mục 755, file 644..."

# Toàn bộ thư mục: thư mục 755, file 644
find . -type d -exec chmod 755 {} \;
find . -type f -exec chmod 644 {} \;

# wp-content/uploads: 775 (dir) / 664 (file) để upload và tạo thư mục con
if [[ -d "wp-content/uploads" ]]; then
  echo "[CHMOD] wp-content/uploads -> 775/664"
  find wp-content/uploads -type d -exec chmod 775 {} \;
  find wp-content/uploads -type f -exec chmod 664 {} \;
fi

echo "[CHMOD] Xong."
CHMOD_EOF
  chmod +x "$DEST"
  info "Script CHMOD đã tạo. Upload lên server và chạy trong thư mục WordPress: ./chmod-on-server.sh"
}

# --- Main: xử lý tham số ---
case "${1:-}" in
  replace-url)
    replace_url "$2" "$3"
    ;;
  export-db)
    export_db
    ;;
  export-db-replace)
    export_db_with_replace "$2" "$3"
    ;;
  zip)
    zip_source
    ;;
  all)
    if [[ -z "$2" || -z "$3" ]]; then
      err "Cách dùng: $0 all <OLD_URL> <NEW_URL>"
      echo "Ví dụ: $0 all 'https://visaminhquan.ddev.site' 'https://visaminhquan.com.vn'"
      exit 1
    fi
    info "Chuẩn bị deploy: chỉ tạo file (không thay đổi DB/DEV)."
    export_db_with_replace "$2" "$3"
    zip_source
    gen_chmod_script
    info "Hoàn tất. Kết quả trong: $OUTPUT_SUBDIR"
    ;;
  chmod-script)
    gen_chmod_script
    info "Script CHMOD: $OUTPUT_SUBDIR/chmod-on-server.sh"
    ;;
  *)
    echo "Cách dùng:"
    echo "  $0 replace-url <OLD_URL> <NEW_URL>   [ẢNH HƯỞNG DEV] Thay URL ngay trong DB hiện tại"
    echo "  $0 export-db                         Export database ra file SQL (giữ nguyên URL)"
    echo "  $0 export-db-replace <OLD> <NEW>    Export DB, thay URL chỉ trong file (không đổi DB)"
    echo "  $0 zip                               ZIP mã nguồn (loại trừ .git, .ddev, node_modules)"
    echo "  $0 chmod-script                      Tạo script chmod-on-server.sh (chạy trên server sau khi upload)"
    echo "  $0 all <OLD_URL> <NEW_URL>           Chuẩn bị deploy: export DB + zip + script CHMOD (không đổi DEV)"
    echo ""
    echo "Kết quả lưu tại: deploy-output/<ngày-giờ>/"
    exit 0
    ;;
esac
