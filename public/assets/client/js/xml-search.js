/**
 * XML Search Integration
 * Provides client-side XML search functionality with debouncing and error handling
 */

(function() {
    'use strict';

    // Configuration
    const CONFIG = {
        searchEndpoint: '/api/xml-search.php',
        debounceDelay: 300,
        minSearchLength: 2
    };

    // State
    let debounceTimer = null;
    let currentRequest = null;

    /**
     * Initialize XML search functionality
     */
    function init() {
        const searchInput = document.querySelector('#search-input, [data-xml-search-input]');
        const resultsContainer = document.querySelector('#xml-search-results, [data-xml-search-results]');
        const loadingIndicator = document.querySelector('#xml-search-loading, [data-xml-search-loading]');

        if (!searchInput || !resultsContainer) {
            console.warn('XML Search: Required elements not found');
            return;
        }

        // Listen to input events
        searchInput.addEventListener('input', function(e) {
            const query = e.target.value.trim();
            handleSearchInput(query, resultsContainer, loadingIndicator);
        });

        // Clear results when input is cleared
        searchInput.addEventListener('blur', function() {
            // Delay to allow click events on results
            setTimeout(function() {
                if (!searchInput.value.trim()) {
                    clearResults(resultsContainer);
                }
            }, 200);
        });
    }

    /**
     * Handle search input with debouncing
     */
    function handleSearchInput(query, resultsContainer, loadingIndicator) {
        // Clear previous timer
        if (debounceTimer) {
            clearTimeout(debounceTimer);
        }

        // Cancel previous request
        if (currentRequest) {
            currentRequest.abort();
            currentRequest = null;
        }

        // Clear results if query is empty
        if (!query || query.length < CONFIG.minSearchLength) {
            clearResults(resultsContainer);
            hideLoading(loadingIndicator);
            return;
        }

        // Show loading indicator
        showLoading(loadingIndicator);

        // Debounce the search request
        debounceTimer = setTimeout(function() {
            performSearch(query, resultsContainer, loadingIndicator);
        }, CONFIG.debounceDelay);
    }

    /**
     * Perform XML search request
     */
    function performSearch(query, resultsContainer, loadingIndicator) {
        const url = CONFIG.searchEndpoint + '?q=' + encodeURIComponent(query);

        currentRequest = new XMLHttpRequest();
        currentRequest.open('GET', url, true);
        currentRequest.setRequestHeader('Accept', 'application/xml');

        currentRequest.onload = function() {
            hideLoading(loadingIndicator);
            currentRequest = null;

            if (this.status === 200) {
                // Log raw response for debugging
                console.log('XML Response:', this.responseText.substring(0, 500));
                
                try {
                    const xmlDoc = parseXML(this.responseText);
                    if (xmlDoc) {
                        renderResults(xmlDoc, resultsContainer);
                    } else {
                        console.error('XML parsing returned null');
                        showError(resultsContainer, 'Không thể phân tích kết quả tìm kiếm');
                    }
                } catch (error) {
                    console.error('XML Search Error:', error);
                    console.error('Response text:', this.responseText.substring(0, 500));
                    showError(resultsContainer, 'Đã xảy ra lỗi khi xử lý kết quả');
                }
            } else {
                console.error('HTTP Error:', this.status, this.statusText);
                showError(resultsContainer, 'Không thể kết nối đến máy chủ');
            }
        };

        currentRequest.onerror = function() {
            hideLoading(loadingIndicator);
            currentRequest = null;
            showError(resultsContainer, 'Lỗi kết nối mạng');
        };

        currentRequest.ontimeout = function() {
            hideLoading(loadingIndicator);
            currentRequest = null;
            showError(resultsContainer, 'Yêu cầu hết thời gian chờ');
        };

        currentRequest.send();
    }

    /**
     * Parse XML response
     */
    function parseXML(xmlString) {
        try {
            const parser = new DOMParser();
            const xmlDoc = parser.parseFromString(xmlString, 'application/xml');

            // Check for parse errors
            const parseError = xmlDoc.querySelector('parsererror');
            if (parseError) {
                console.error('XML Parse Error:', parseError.textContent);
                return null;
            }

            return xmlDoc;
        } catch (error) {
            console.error('XML Parsing Exception:', error);
            return null;
        }
    }

    /**
     * Render search results
     */
    function renderResults(xmlDoc, resultsContainer) {
        // Check for error element
        const errorElement = xmlDoc.querySelector('error');
        if (errorElement) {
            showError(resultsContainer, errorElement.textContent || 'Đã xảy ra lỗi');
            return;
        }

        // Extract product elements
        const products = xmlDoc.querySelectorAll('product');

        if (products.length === 0) {
            showNoResults(resultsContainer);
            return;
        }

        // Build HTML for products
        let html = '<div class="xml-search-results-list">';

        products.forEach(function(product) {
            const id = getElementText(product, 'id');
            const name = getElementText(product, 'name');
            const price = getElementText(product, 'price');
            const image = getElementText(product, 'image');
            const slug = getElementText(product, 'slug');

            const productUrl = '/san-pham/' + slug;
            const formattedPrice = formatPrice(price);

            html += '<a href="' + escapeHtml(productUrl) + '" class="xml-search-result-item">';
            html += '  <div class="xml-search-result-image">';
            if (image) {
                html += '    <img src="' + escapeHtml(image) + '" alt="' + escapeHtml(name) + '" loading="lazy">';
            } else {
                html += '    <div class="xml-search-result-no-image">No Image</div>';
            }
            html += '  </div>';
            html += '  <div class="xml-search-result-info">';
            html += '    <div class="xml-search-result-name">' + escapeHtml(name) + '</div>';
            html += '    <div class="xml-search-result-price">' + formattedPrice + '</div>';
            html += '  </div>';
            html += '</a>';
        });

        html += '</div>';

        resultsContainer.innerHTML = html;
        resultsContainer.style.display = 'block';
    }

    /**
     * Get text content from XML element
     */
    function getElementText(parent, tagName) {
        const element = parent.querySelector(tagName);
        return element ? element.textContent : '';
    }

    /**
     * Format price
     */
    function formatPrice(price) {
        const numPrice = parseFloat(price);
        if (isNaN(numPrice)) {
            return 'Liên hệ';
        }
        return numPrice.toLocaleString('vi-VN') + 'đ';
    }

    /**
     * Escape HTML to prevent XSS
     */
    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    /**
     * Show loading indicator
     */
    function showLoading(loadingIndicator) {
        if (loadingIndicator) {
            loadingIndicator.style.display = 'block';
        }
    }

    /**
     * Hide loading indicator
     */
    function hideLoading(loadingIndicator) {
        if (loadingIndicator) {
            loadingIndicator.style.display = 'none';
        }
    }

    /**
     * Clear results
     */
    function clearResults(resultsContainer) {
        resultsContainer.innerHTML = '';
        resultsContainer.style.display = 'none';
    }

    /**
     * Show error message
     */
    function showError(resultsContainer, message) {
        resultsContainer.innerHTML = '<div class="xml-search-error">' + escapeHtml(message) + '</div>';
        resultsContainer.style.display = 'block';
    }

    /**
     * Show no results message
     */
    function showNoResults(resultsContainer) {
        resultsContainer.innerHTML = '<div class="xml-search-no-results">Không tìm thấy sản phẩm nào</div>';
        resultsContainer.style.display = 'block';
    }

    // Initialize on DOM ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

})();
