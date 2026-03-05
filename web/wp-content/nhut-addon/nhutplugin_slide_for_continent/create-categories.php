<?php
/**
 * Script tạo categories cho Visa theo châu lục
 * 
 * Chạy script này một lần để tạo tất cả categories
 * Có thể chạy qua WP-CLI hoặc thêm vào functions.php tạm thời
 */

if (!defined('ABSPATH')) {
    require_once('../../../wp-load.php');
}

/**
 * Tạo categories cho Visa
 */
function nhut_create_visa_categories() {
    // Định nghĩa cấu trúc categories
    $categories = array(
        'VISA CHÂU Á' => array(
            'VISA ẤN ĐỘ',
            'VISA NHẬT BẢN',
            'VISA ĐÀI LOAN',
            'VISA HỒNG KONG',
            'VISA TRUNG QUỐC',
            'VISA DUBAI',
            'VISA NGA'
        ),
        'VISA CHÂU ÂU' => array(
            'VISA BỈ',
            'VISA ANH',
            'VISA ĐỨC',
            'VISA Ý',
            'VISA PHÁP',
            'VISA SLOVENIA',
            'VISA BỒ ĐÀO NHA',
            'VISA HÀ LAN',
            'VISA THỤY ĐIỂN',
            'VISA ĐAN MẠCH',
            'VISA IRELAND',
            'VISA PHẦN LAN',
            'VISA ÁO',
            'VISA HY LẠP',
            'VISA ICELAND',
            'VISA NA UY',
            'VISA BULGARIA',
            'VISA HUNGARY',
            'VISA BA LAN',
            'VISA LITHUANIA',
            'VISA THỤY SĨ',
            'VISA LIECHTENSTEIN',
            'VISA CỘNG HÒA SÍP'
        ),
        'VISA CHÂU MỸ' => array(
            'VISA ÚC', // Note: Úc thường thuộc Châu Úc nhưng trong menu có ở đây
            'VISA ARGENTINA',
            'VISA FIJI',
            'VISA PERU',
            'VISA MỸ',
            'VISA CANADA'
        ),
        'VISA CHÂU PHI' => array(
            'VISA AI CẬP'
        ),
        'VISA CHÂU ÚC' => array(
            'VISA ÚC',
            'VISA NEW ZEALAND'
        )
    );
    
    $created_categories = array();
    
    foreach ($categories as $parent_name => $children) {
        // Tạo parent category
        $parent_id = category_exists($parent_name);
        
        if (!$parent_id) {
            $parent_id = wp_create_category($parent_name);
            if (is_wp_error($parent_id)) {
                echo "Lỗi tạo category: {$parent_name}\n";
                continue;
            }
            echo "Đã tạo category: {$parent_name} (ID: {$parent_id})\n";
        } else {
            echo "Category đã tồn tại: {$parent_name} (ID: {$parent_id})\n";
        }
        
        $created_categories[$parent_name] = $parent_id;
        
        // Tạo child categories
        foreach ($children as $child_name) {
            $child_id = category_exists($child_name, $parent_id);
            
            if (!$child_id) {
                $child_id = wp_create_category($child_name, $parent_id);
                if (is_wp_error($child_id)) {
                    echo "  Lỗi tạo sub-category: {$child_name}\n";
                    continue;
                }
                echo "  Đã tạo sub-category: {$child_name} (ID: {$child_id})\n";
            } else {
                echo "  Sub-category đã tồn tại: {$child_name} (ID: {$child_id})\n";
            }
        }
    }
    
    return $created_categories;
}

// Chạy nếu được gọi trực tiếp
if (php_sapi_name() === 'cli' || (isset($_GET['run_create_categories']) && current_user_can('manage_categories'))) {
    nhut_create_visa_categories();
    echo "\nHoàn thành!\n";
}

