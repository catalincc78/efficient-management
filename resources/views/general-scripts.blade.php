<script type="module">
     window.showNotification = function(containerSelector, arMessages, type = 'success'){
        $(containerSelector).html('<div class="alert alert-' + type +'" role="alert">' + arMessages.join('<br>') + '</div>');
        setTimeout(function(){
            $(containerSelector).html('');
        },3000);
    }

     window.setInputFilter = function(textbox, inputFilter, errMsg) {
         [ "input", "keydown", "keyup", "mousedown", "mouseup", "select", "contextmenu", "drop", "focusout" ].forEach(function(event) {
             $(document).on(event, textbox, function(e) {
                 if (inputFilter(this.value)) {
                     if ([ "keydown", "mousedown", "focusout" ].indexOf(e.type) >= 0){
                         this.classList.remove("input-error");
                         this.setCustomValidity("");
                     }

                     this.oldValue = this.value;
                     this.oldSelectionStart = this.selectionStart;
                     this.oldSelectionEnd = this.selectionEnd;
                 } else if (this.hasOwnProperty("oldValue")) {
                     this.classList.add("input-error");
                     this.setCustomValidity(errMsg);
                     this.reportValidity();
                     this.value = this.oldValue;
                     this.setSelectionRange(this.oldSelectionStart, this.oldSelectionEnd);
                 } else {
                     this.value = "";
                 }
             });
         });
     }
     setInputFilter('.input-type-float', function(value) {
         return /^\d*\.?\d*$/.test(value);
     }, '{{ __('Type only digits and floating point!') }}');
     setInputFilter('.input-type-int', function(value) {
         return /^\d*$/.test(value);
     }, '{{ __('Type only digits!') }}');

     window.createDatePicker =  function(pickerSelector, onChangeCallback = null, minDate = null, maxDate = null) {
         let bIsStart = pickerSelector.includes('start');
         let arSplitSelector = bIsStart ? pickerSelector.split('start') : pickerSelector.split('end');
         let sSuffix = (arSplitSelector.length > 1) ? arSplitSelector[1] : '';
         let reversePickerSelector = arSplitSelector[0] + (bIsStart ? 'end' : 'start') + sSuffix;
         let instanceSelector = bIsStart ? 'startDatePicker' + sSuffix : 'endDatePicker' + sSuffix;
         let currentDate = new Date();
         if (window[instanceSelector] !== undefined) {
             currentDate = window[instanceSelector].getDate();
             window[instanceSelector].destroy();
         }
         if (bIsStart) {
             maxDate = (maxDate === null) ? currentDate : maxDate;
         } else {
             minDate = (minDate === null) ? currentDate : minDate;
         }
         let currentPicker = new easepick.create({
             element: $(pickerSelector)[0],
             css: [
                 'https://cdn.jsdelivr.net/npm/@easepick/core@1.2.1/dist/index.css',
                 'https://cdn.jsdelivr.net/npm/@easepick/lock-plugin@1.2.1/dist/index.css',
             ],
             plugins: [LockPlugin],
             LockPlugin: {
                 minDate: minDate,
                 maxDate: maxDate
             },
             zIndex: 4,
             date: currentDate,
             setup(picker) {
                 picker.on('select', function (e) {
                     if (bIsStart) {
                         createDatePicker(reversePickerSelector, onChangeCallback, e.detail.date);
                     } else {
                         createDatePicker(reversePickerSelector, onChangeCallback, null, e.detail.date);
                     }
                     if(onChangeCallback){
                         onChangeCallback();
                     }
                 })
             }
         });
         window[instanceSelector] = currentPicker;
     }
</script>
