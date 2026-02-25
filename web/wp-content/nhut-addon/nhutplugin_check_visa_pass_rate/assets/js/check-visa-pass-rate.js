/**
 * Check Visa Pass Rate Form JavaScript
 */

(function($) {
    'use strict';
    
    $(document).ready(function() {
        // Initialize form for each instance
        $('.nhut-visa-form-wrapper').each(function() {
            initVisaForm($(this));
        });
    });
    
    function initVisaForm($wrapper) {
        const $form = $wrapper.find('.nhut-visa-form');
        const $steps = $form.find('.nhut-form-step');
        const $progressSteps = $wrapper.find('.nhut-progress-step');
        let currentStep = 1;
        const totalSteps = $steps.length;
        
        // Generate random captcha numbers
        const captchaNum1 = Math.floor(Math.random() * 10) + 1;
        const captchaNum2 = Math.floor(Math.random() * 10) + 1;
        $wrapper.find('.nhut-captcha-num1').text(captchaNum1);
        $wrapper.find('.nhut-captcha-num2').text(captchaNum2);
        
        // Store captcha values in hidden inputs for validation
        $form.append('<input type="hidden" name="captcha_num1" value="' + captchaNum1 + '">');
        $form.append('<input type="hidden" name="captcha_num2" value="' + captchaNum2 + '">');
        
        // Next button handler
        $form.on('click', '.nhut-btn-next', function(e) {
            e.preventDefault();
            const $currentStepEl = $steps.filter('.nhut-step-active');
            
            // Validate current step
            if (!validateStep($currentStepEl)) {
                return;
            }
            
            // Move to next step
            if (currentStep < totalSteps) {
                goToStep(currentStep + 1);
            }
        });
        
        // Back button handler
        $form.on('click', '.nhut-btn-back', function(e) {
            e.preventDefault();
            if (currentStep > 1) {
                goToStep(currentStep - 1);
            }
        });
        
        // Form submit handler
        $form.on('submit', function(e) {
            e.preventDefault();
            
            const $currentStepEl = $steps.filter('.nhut-step-active');
            
            // Validate current step
            if (!validateStep($currentStepEl)) {
                return;
            }
            
            // Submit form
            submitForm($form);
        });
        
        function validateStep($stepEl) {
            let isValid = true;
            const $requiredFields = $stepEl.find('[required]');
            
            $requiredFields.each(function() {
                const $field = $(this);
                const value = $field.val();
                
                // Remove previous error styling
                $field.removeClass('nhut-field-error');
                
                if (!value || value.trim() === '') {
                    isValid = false;
                    $field.addClass('nhut-field-error');
                    // Add visual feedback
                    $field.css('border', '2px solid #ff0000');
                    setTimeout(function() {
                        $field.css('border', '');
                    }, 2000);
                }
            });
            
            // Special validation for email
            const $emailField = $stepEl.find('input[type="email"]');
            if ($emailField.length && $emailField.val()) {
                const email = $emailField.val();
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailRegex.test(email)) {
                    isValid = false;
                    $emailField.addClass('nhut-field-error');
                    $emailField.css('border', '2px solid #ff0000');
                    setTimeout(function() {
                        $emailField.css('border', '');
                    }, 2000);
                }
            }
            
            // Special validation for phone
            const $phoneField = $stepEl.find('input[type="tel"]');
            if ($phoneField.length && $phoneField.val()) {
                const phone = $phoneField.val().replace(/\s/g, '');
                if (phone.length < 10) {
                    isValid = false;
                    $phoneField.addClass('nhut-field-error');
                    $phoneField.css('border', '2px solid #ff0000');
                    setTimeout(function() {
                        $phoneField.css('border', '');
                    }, 2000);
                }
            }
            
            return isValid;
        }
        
        function goToStep(step) {
            // Hide current step
            $steps.removeClass('nhut-step-active');
            $steps.filter('[data-step="' + currentStep + '"]').removeClass('nhut-step-active');
            
            // Show new step
            $steps.filter('[data-step="' + step + '"]').addClass('nhut-step-active');
            
            // Update progress indicator
            $progressSteps.each(function() {
                const stepNum = parseInt($(this).data('step'));
                $(this).removeClass('nhut-step-active nhut-step-completed');
                
                if (stepNum < step) {
                    $(this).addClass('nhut-step-completed');
                } else if (stepNum === step) {
                    $(this).addClass('nhut-step-active');
                }
            });
            
            currentStep = step;
            
            // Scroll to top of form
            $('html, body').animate({
                scrollTop: $wrapper.offset().top - 50
            }, 300);
        }
        
        function submitForm($form) {
            // Disable submit button
            const $submitBtn = $form.find('.nhut-btn-submit');
            $submitBtn.prop('disabled', true);
            $form.addClass('nhut-form-submitting');
            
            // Collect all form data
            const formData = {
                action: 'nhut_submit_visa_form',
                nonce: nhutVisaForm.nonce,
                visa_country: $form.find('[name="visa_country"]').val(),
                traveled_before: $form.find('[name="traveled_before"]').val(),
                savings: $form.find('[name="savings"]').val(),
                property_car: $form.find('[name="property_car"]').val(),
                current_job: $form.find('[name="current_job"]').val(),
                full_name: $form.find('[name="full_name"]').val(),
                email: $form.find('[name="email"]').val(),
                phone: $form.find('[name="phone"]').val(),
                captcha_answer: parseInt($form.find('[name="captcha_answer"]').val()),
                captcha_num1: parseInt($form.find('[name="captcha_num1"]').val()),
                captcha_num2: parseInt($form.find('[name="captcha_num2"]').val())
            };
            
            // Submit via AJAX
            $.ajax({
                url: nhutVisaForm.ajaxUrl,
                type: 'POST',
                data: formData,
                success: function(response) {
                    if (response.success) {
                        showSuccessModal();
                        // Reset form
                        $form[0].reset();
                        goToStep(1);
                    } else {
                        showErrorModal(response.data && response.data.message ? response.data.message : 'Có lỗi xảy ra');
                    }
                },
                error: function(xhr, status, error) {
                    showErrorModal('Có lỗi xảy ra khi gửi form. Vui lòng thử lại sau.');
                },
                complete: function() {
                    $submitBtn.prop('disabled', false);
                    $form.removeClass('nhut-form-submitting');
                }
            });
        }
        
        function showSuccessModal() {
            const $modal = $('#vmq-modal-success');
            if ($modal.length) {
                // Hide all modals first
                $('.vmq-modal').css('display', 'none');
                
                // Show success modal
                $modal.css('display', 'flex');
                $('body').css('overflow', 'hidden');
                
                // Reset and start cooldown
                const $cooldownTime = $modal.find('.vmq-cooldown-time');
                const $progress = $modal.find('.vmq-cooldown-progress');
                
                if ($cooldownTime.length) {
                    $cooldownTime.text('5');
                }
                if ($progress.length) {
                    $progress.css('width', '0%');
                    $progress.css('animation', 'none');
                    // Trigger reflow
                    $progress[0].offsetHeight;
                    $progress.css('animation', 'vmq-cooldown-countdown 5s linear forwards');
                }
                
                startCooldown($modal);
            }
        }
        
        function showErrorModal(message) {
            const $modal = $('#vmq-modal-error');
            if ($modal.length) {
                // Hide all modals first
                $('.vmq-modal').css('display', 'none');
                
                // Show error modal
                $modal.css('display', 'flex');
                $('body').css('overflow', 'hidden');
                
                // Reset and start cooldown
                const $cooldownTime = $modal.find('.vmq-cooldown-time');
                const $progress = $modal.find('.vmq-cooldown-progress');
                
                if ($cooldownTime.length) {
                    $cooldownTime.text('5');
                }
                if ($progress.length) {
                    $progress.css('width', '0%');
                    $progress.css('animation', 'none');
                    // Trigger reflow
                    $progress[0].offsetHeight;
                    $progress.css('animation', 'vmq-cooldown-countdown 5s linear forwards');
                }
                
                startCooldown($modal);
            }
        }
        
        function startCooldown($modal) {
            let seconds = 5;
            const $cooldownTime = $modal.find('.vmq-cooldown-time');
            const $progress = $modal.find('.vmq-cooldown-progress');
            
            const interval = setInterval(function() {
                seconds--;
                if ($cooldownTime.length) {
                    $cooldownTime.text(seconds);
                }
                
                if (seconds <= 0) {
                    clearInterval(interval);
                    $modal.css('display', 'none');
                    $('body').css('overflow', '');
                    // Reset cooldown
                    if ($cooldownTime.length) {
                        $cooldownTime.text('5');
                    }
                    if ($progress.length) {
                        $progress.css('width', '0%');
                        $progress.css('animation', 'none');
                    }
                }
            }, 1000);
        }
    }
    
})(jQuery);

