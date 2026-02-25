/**
 * Crawler Admin JavaScript
 */

(function($) {
    'use strict';
    
    let isRunning = false;
    let progressInterval = null;
    
    $(document).ready(function() {
        // Update site name when selection changes
        $('#nhut-crawler-site').on('change', function() {
            const selectedText = $(this).find('option:selected').text();
            $('#nhut-current-site-name').text(selectedText);
            // Clear scan result when site changes
            $('#nhut-scan-result').text('');
            $('#nhut-crawler-start').prop('disabled', true);
        });
        
        // Scan button
        $('#nhut-crawler-scan').on('click', function() {
            scanPosts();
        });
        
        // Start button
        $('#nhut-crawler-start').on('click', function() {
            startCrawler();
        });
        
        // Stop button
        $('#nhut-crawler-stop').on('click', function() {
            stopCrawler();
        });
        
        // Truncate button
        $('#nhut-crawler-truncate').on('click', function() {
            if (!confirm('Bạn có chắc chắn muốn xóa TẤT CẢ các bài viết đã crawler? Hành động này không thể hoàn tác!')) {
                return;
            }
            
            const $btn = $(this);
            $btn.prop('disabled', true).text('Đang xóa...');
            
            let deletedCount = 0;
            let deletedImages = 0;
            let offset = 0;
            let imageOffset = 0;
            let deleteImages = false;
            
            function processTruncate() {
                const data = {
                    action: 'nhut_crawler_truncate',
                    nonce: nhutCrawler.nonce,
                    offset: offset,
                    deleted_count: deletedCount,
                    deleted_images: deletedImages
                };
                
                if (deleteImages) {
                    data.delete_images = true;
                    data.image_offset = imageOffset;
                }
                
                $.ajax({
                    url: nhutCrawler.ajaxUrl,
                    type: 'POST',
                    data: data,
                    timeout: 300000, // 5 minutes
                    success: function(response) {
                        if (response.success) {
                            const result = response.data;
                            deletedCount = result.deleted_posts || deletedCount;
                            deletedImages = result.deleted_images || deletedImages;
                            
                            if (result.continue) {
                                if (result.status === 'processing_images') {
                                    imageOffset = result.image_offset || imageOffset;
                                    deleteImages = true;
                                } else {
                                    offset = result.offset || offset;
                                }
                                
                                addLog(result.message, 'info');
                                $btn.text('Đang xóa... (' + deletedCount + ' bài viết, ' + deletedImages + ' hình ảnh)');
                                
                                // Continue with next batch
                                setTimeout(processTruncate, 500);
                            } else {
                                // Completed
                                addLog(result.message, 'success');
                                alert(result.message);
                                location.reload();
                            }
                        } else {
                            addLog('Lỗi: ' + response.data.message, 'error');
                            alert('Lỗi: ' + response.data.message);
                            $btn.prop('disabled', false).text('Xóa tất cả bài viết Crawler');
                        }
                    },
                    error: function(xhr, status, error) {
                        if (status === 'timeout') {
                            addLog('Timeout - tiếp tục xử lý...', 'info');
                            setTimeout(processTruncate, 1000);
                        } else {
                            addLog('Lỗi kết nối: ' + error, 'error');
                            alert('Lỗi kết nối: ' + error);
                            $btn.prop('disabled', false).text('Xóa tất cả bài viết Crawler');
                        }
                    }
                });
            }
            
            // Start processing
            processTruncate();
        });
        
        // Check for existing progress on page load
        checkProgress();
    });
    
    function scanPosts() {
        const $btn = $('#nhut-crawler-scan');
        const $result = $('#nhut-scan-result');
        
        $btn.prop('disabled', true).text('Đang quét...');
        $result.text('Đang quét danh sách bài viết...');
        
        const selectedSite = $('#nhut-crawler-site').val();
        
        $.ajax({
            url: nhutCrawler.ajaxUrl,
            type: 'POST',
            data: {
                action: 'nhut_crawler_scan',
                nonce: nhutCrawler.nonce,
                site: selectedSite
            },
            success: function(response) {
                $btn.prop('disabled', false).text('Quét danh sách bài viết');
                
                if (response.success) {
                    const data = response.data;
                    $result.html(
                        '<strong style="color: #2271b1;">' + data.message + '</strong><br>' +
                        '<small>Tổng: ' + data.total + ' | Mới: ' + data.new + ' | Đã crawl: ' + data.already_crawled + '</small>'
                    );
                    $('#nhut-crawler-start').prop('disabled', false);
                } else {
                    $result.html('<span style="color: #d63638;">Lỗi: ' + response.data.message + '</span>');
                }
            },
            error: function() {
                $btn.prop('disabled', false).text('Quét danh sách bài viết');
                $result.html('<span style="color: #d63638;">Lỗi kết nối khi quét</span>');
            }
        });
    }
    
    function startCrawler() {
        if (isRunning) {
            return;
        }
        
        isRunning = true;
        $('#nhut-crawler-start').prop('disabled', true);
        $('#nhut-crawler-stop').show();
        $('#nhut-crawler-progress').show();
        
        addLog('Khởi động crawler...', 'info');
        
        // Get selected site and limit
        const selectedSite = $('#nhut-crawler-site').val();
        const limit = parseInt($('#nhut-crawler-limit').val()) || 0;
        
        // Step 1: Initialize
        $.ajax({
            url: nhutCrawler.ajaxUrl,
            type: 'POST',
            data: {
                action: 'nhut_crawler_start',
                nonce: nhutCrawler.nonce,
                site: selectedSite,
                limit: limit
            },
            success: function(response) {
                if (response.success) {
                    // Display log messages from scan/start
                    if (response.data.log_messages && response.data.log_messages.length > 0) {
                        response.data.log_messages.forEach(function(logMsg) {
                            addLog(logMsg.message, logMsg.type || 'info');
                        });
                    }
                    
                    addLog('Đã sẵn sàng crawl ' + response.data.total + ' bài viết', 'success');
                    updateProgress(0, response.data.total, 'Đang bắt đầu...');
                    
                    // Start processing
                    processNext();
                    
                    // Start progress polling
                    startProgressPolling();
                } else {
                    addLog('Lỗi: ' + response.data.message, 'error');
                    stopCrawler();
                }
            },
            error: function() {
                addLog('Lỗi kết nối', 'error');
                stopCrawler();
            }
        });
    }
    
    function processNext() {
        if (!isRunning) {
            return;
        }
        
        $.ajax({
            url: nhutCrawler.ajaxUrl,
            type: 'POST',
            data: {
                action: 'nhut_crawler_process',
                nonce: nhutCrawler.nonce
            },
            timeout: 300000, // 5 minutes
            success: function(response) {
                if (response.success) {
                    const data = response.data;
                    
                    if (data.status === 'completed') {
                        addLog('Hoàn thành! Đã insert ' + data.inserted + ' bài viết', 'success');
                        stopCrawler();
                        return;
                    }
                    
                    // Update progress
                    updateProgress(data.current, data.total, data.message);
                    
                    // Display log messages from backend (includes category info)
                    if (data.log_messages && data.log_messages.length > 0) {
                        data.log_messages.forEach(function(logMsg) {
                            addLog(logMsg.message, logMsg.type || 'success');
                        });
                    } else {
                        // Fallback: use message directly
                        addLog(data.message, 'success');
                    }
                    
                    // Process next with delay to avoid timeout
                    setTimeout(function() {
                        processNext();
                    }, 1000); // 1 second delay between posts
                    
                } else {
                    addLog('Lỗi: ' + response.data.message, 'error');
                    setTimeout(function() {
                        processNext(); // Continue despite error
                    }, 2000);
                }
            },
            error: function(xhr, status, error) {
                if (status === 'timeout') {
                    addLog('Timeout - tiếp tục xử lý...', 'info');
                    setTimeout(function() {
                        processNext();
                    }, 2000);
                } else {
                    addLog('Lỗi: ' + error, 'error');
                    setTimeout(function() {
                        processNext(); // Continue despite error
                    }, 2000);
                }
            }
        });
    }
    
    function startProgressPolling() {
        if (progressInterval) {
            clearInterval(progressInterval);
        }
        
        progressInterval = setInterval(function() {
            if (!isRunning) {
                clearInterval(progressInterval);
                return;
            }
            
            $.ajax({
                url: nhutCrawler.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'nhut_crawler_get_progress',
                    nonce: nhutCrawler.nonce
                },
                success: function(response) {
                    if (response.success) {
                        const data = response.data;
                        updateProgress(data.current, data.total, data.message);
                        
                        // Display log messages if available
                        if (data.log_messages && data.log_messages.length > 0) {
                            const $log = $('#nhut-progress-log');
                            const lastLogText = $log.find('.log-entry:last').text();
                            
                            data.log_messages.forEach(function(logMsg) {
                                // Only add if not already displayed (avoid duplicates)
                                if (!lastLogText || lastLogText.indexOf(logMsg.message) === -1) {
                                    addLog(logMsg.message, logMsg.type || 'info');
                                }
                            });
                        }
                        
                        if (data.status === 'completed') {
                            stopCrawler();
                        }
                    }
                }
            });
        }, 2000); // Poll every 2 seconds
    }
    
    function stopCrawler() {
        isRunning = false;
        $('#nhut-crawler-start').prop('disabled', false);
        $('#nhut-crawler-stop').hide();
        
        if (progressInterval) {
            clearInterval(progressInterval);
            progressInterval = null;
        }
        
        addLog('Đã dừng crawler', 'info');
    }
    
    function updateProgress(current, total, message) {
        const percent = total > 0 ? (current / total * 100) : 0;
        $('#nhut-progress-fill').css('width', percent + '%');
        $('#nhut-progress-text').text(message + ' (' + current + '/' + total + ')');
    }
    
    function addLog(message, type) {
        const log = $('#nhut-progress-log');
        const timestamp = new Date().toLocaleTimeString();
        const entry = $('<div class="log-entry log-' + type + '">[' + timestamp + '] ' + message + '</div>');
        log.append(entry);
        log.scrollTop(log[0].scrollHeight);
    }
    
    function checkProgress() {
        $.ajax({
            url: nhutCrawler.ajaxUrl,
            type: 'POST',
            data: {
                action: 'nhut_crawler_get_progress',
                nonce: nhutCrawler.nonce
            },
            success: function(response) {
                if (response.success && response.data.status !== 'idle') {
                    const data = response.data;
                    $('#nhut-crawler-progress').show();
                    updateProgress(data.current, data.total, data.message);
                    
                    if (data.status === 'processing') {
                        // Resume crawling
                        isRunning = true;
                        $('#nhut-crawler-start').prop('disabled', true);
                        $('#nhut-crawler-stop').show();
                        processNext();
                        startProgressPolling();
                    }
                }
            }
        });
    }
    
})(jQuery);

