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

</script>
