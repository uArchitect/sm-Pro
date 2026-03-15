/**
 * SearchableSelect — Arama yapılabilir select (Select2 benzeri, vanilla JS)
 * Kullanım: SearchableSelect.enhance(selectEl) veya SearchableSelect.enhanceAll('.searchable-select')
 * Form gönderiminde orijinal select kullanılır; değer senkron tutulur.
 */
(function (global) {
    'use strict';

    var DOC = document;

    function normalizeForSearch(str) {
        if (typeof str !== 'string') return '';
        var map = { 'ı': 'i', 'İ': 'i', 'ğ': 'g', 'Ğ': 'g', 'ü': 'u', 'Ü': 'u', 'ş': 's', 'Ş': 's', 'ö': 'o', 'Ö': 'o', 'ç': 'c', 'Ç': 'c' };
        return str.replace(/[ıİğĞüÜşŞöÖçÇ]/g, function (c) { return map[c] || c; }).toLowerCase();
    }

    function escapeHtml(str) {
        if (typeof str !== 'string') return '';
        var div = DOC.createElement('div');
        div.textContent = str;
        return div.innerHTML;
    }

    function getSelectOptions(select) {
        var options = [];
        for (var i = 0; i < select.options.length; i++) {
            var opt = select.options[i];
            options.push({ value: opt.value, text: opt.text.trim(), normalized: normalizeForSearch(opt.text.trim()) });
        }
        return options;
    }

    function getSelectedText(select, options) {
        var val = select.value;
        for (var j = 0; j < options.length; j++) {
            if (options[j].value === val) return options[j].text;
        }
        return '';
    }

    function createDropdown(options, selectedValue, searchPlaceholder) {
        var searchPlaceholderText = searchPlaceholder || 'Ara...';
        var html = '<div class="ss-dropdown" role="listbox">' +
            '<input type="text" class="ss-search form-control form-control-sm" placeholder="' + escapeHtml(searchPlaceholderText) + '" autocomplete="off" aria-label="Ara">' +
            '<div class="ss-list" role="list"></div>' +
            '</div>';
        var wrap = DOC.createElement('div');
        wrap.innerHTML = html;
        return wrap.firstElementChild;
    }

    function renderList(listEl, options, query, selectedValue) {
        var q = normalizeForSearch(query);
        var fragment = DOC.createDocumentFragment();
        var count = 0;
        for (var i = 0; i < options.length; i++) {
            var opt = options[i];
            if (q && opt.normalized.indexOf(q) === -1) continue;
            count++;
            var div = DOC.createElement('div');
            div.className = 'ss-option' + (opt.value === selectedValue ? ' ss-selected' : '');
            div.setAttribute('role', 'option');
            div.setAttribute('data-value', opt.value);
            div.textContent = opt.text;
            if (!opt.value) div.classList.add('ss-option-placeholder');
            fragment.appendChild(div);
        }
        listEl.innerHTML = '';
        listEl.appendChild(fragment);
        return count;
    }

    function enhance(selectEl, config) {
        if (!selectEl || selectEl.tagName !== 'SELECT') return null;
        if (selectEl.dataset.searchableSelect === 'enhanced') return null;

        config = config || {};
        var placeholder = config.placeholder !== undefined ? config.placeholder : 'Seçin...';
        var searchPlaceholder = config.searchPlaceholder !== undefined ? config.searchPlaceholder : 'Ara...';

        var options = getSelectOptions(selectEl);
        if (options.length === 0) return null;

        selectEl.dataset.searchableSelect = 'enhanced';
        selectEl.classList.add('ss-native');

        var wrapper = DOC.createElement('div');
        wrapper.className = 'ss-wrapper';
        selectEl.parentNode.insertBefore(wrapper, selectEl);
        wrapper.appendChild(selectEl);

        var trigger = DOC.createElement('button');
        trigger.type = 'button';
        trigger.className = 'ss-trigger form-select form-select-sm';
        trigger.setAttribute('aria-haspopup', 'listbox');
        trigger.setAttribute('aria-expanded', 'false');
        var triggerText = DOC.createElement('span');
        triggerText.className = 'ss-trigger-text';
        trigger.appendChild(triggerText);
        var triggerIcon = DOC.createElement('span');
        triggerIcon.className = 'ss-trigger-icon';
        triggerIcon.setAttribute('aria-hidden', 'true');
        triggerIcon.innerHTML = '<i class="bi bi-chevron-down"></i>';
        trigger.appendChild(triggerIcon);
        wrapper.appendChild(trigger);

        var dropdown = createDropdown(options, selectEl.value, searchPlaceholder);
        wrapper.appendChild(dropdown);

        var searchInput = dropdown.querySelector('.ss-search');
        var listEl = dropdown.querySelector('.ss-list');

        function updateTriggerText() {
            var text = getSelectedText(selectEl, options);
            triggerText.textContent = text || placeholder;
            triggerText.classList.toggle('ss-placeholder', !text);
        }

        function open() {
            if (dropdown.classList.contains('ss-open')) return;
            dropdown.classList.add('ss-open');
            trigger.setAttribute('aria-expanded', 'true');
            renderList(listEl, options, '', selectEl.value);
            searchInput.value = '';
            listEl.querySelectorAll('.ss-option').forEach(function (o) { o.classList.remove('ss-highlight'); });
            var toHighlight = listEl.querySelector('.ss-selected') || listEl.querySelector('.ss-option');
            if (toHighlight) toHighlight.classList.add('ss-highlight');
            searchInput.focus();
            DOC.addEventListener('click', closeOnOutsideClick);
            DOC.addEventListener('keydown', handleDocKeydown);
        }

        function close() {
            if (!dropdown.classList.contains('ss-open')) return;
            dropdown.classList.remove('ss-open');
            trigger.setAttribute('aria-expanded', 'false');
            trigger.focus();
            DOC.removeEventListener('click', closeOnOutsideClick);
            DOC.removeEventListener('keydown', handleDocKeydown);
        }

        function closeOnOutsideClick(e) {
            if (wrapper.contains(e.target)) return;
            close();
        }

        function selectValue(value) {
            selectEl.value = value;
            updateTriggerText();
            close();
            var ev = new Event('change', { bubbles: true });
            selectEl.dispatchEvent(ev);
        }

        function handleDocKeydown(e) {
            if (e.key === 'Escape') {
                e.preventDefault();
                close();
            }
        }

        trigger.addEventListener('click', function (e) {
            e.preventDefault();
            if (dropdown.classList.contains('ss-open')) close();
            else open();
        });

        searchInput.addEventListener('input', function () {
            renderList(listEl, options, searchInput.value, selectEl.value);
            listEl.querySelectorAll('.ss-option').forEach(function (o) { o.classList.remove('ss-highlight'); });
            var first = listEl.querySelector('.ss-option');
            if (first) first.classList.add('ss-highlight');
        });

        searchInput.addEventListener('keydown', function (e) {
            var opts = listEl.querySelectorAll('.ss-option');
            var cur = listEl.querySelector('.ss-option.ss-highlight');
            var idx = cur ? Array.prototype.indexOf.call(opts, cur) : -1;

            if (e.key === 'ArrowDown') {
                e.preventDefault();
                idx = idx < opts.length - 1 ? idx + 1 : 0;
                opts[idx].classList.add('ss-highlight');
                if (cur) cur.classList.remove('ss-highlight');
                opts[idx].scrollIntoView({ block: 'nearest' });
            } else if (e.key === 'ArrowUp') {
                e.preventDefault();
                idx = idx <= 0 ? opts.length - 1 : idx - 1;
                opts[idx].classList.add('ss-highlight');
                if (cur) cur.classList.remove('ss-highlight');
                opts[idx].scrollIntoView({ block: 'nearest' });
            } else if (e.key === 'Enter' && cur) {
                e.preventDefault();
                selectValue(cur.getAttribute('data-value'));
            }
        });

        listEl.addEventListener('click', function (e) {
            var opt = e.target.closest('.ss-option');
            if (!opt) return;
            selectValue(opt.getAttribute('data-value'));
        });

        listEl.addEventListener('mouseover', function (e) {
            var opt = e.target.closest('.ss-option');
            if (!opt) return;
            listEl.querySelectorAll('.ss-option').forEach(function (o) { o.classList.remove('ss-highlight'); });
            opt.classList.add('ss-highlight');
        });

        trigger.addEventListener('keydown', function (e) {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                open();
            }
        });

        updateTriggerText();

        return { select: selectEl, close: close, updateTriggerText: updateTriggerText };
    }

    function enhanceAll(selectorOrElement, config) {
        var el = typeof selectorOrElement === 'string' ? DOC.querySelector(selectorOrElement) : selectorOrElement;
        if (!el) return [];
        var selects = el.querySelectorAll ? el.querySelectorAll('select') : [el];
        var results = [];
        selects.forEach(function (s) {
            if (s.tagName === 'SELECT') {
                var r = enhance(s, config);
                if (r) results.push(r);
            }
        });
        return results;
    }

    global.SearchableSelect = {
        enhance: enhance,
        enhanceAll: enhanceAll
    };
})(typeof window !== 'undefined' ? window : this);
