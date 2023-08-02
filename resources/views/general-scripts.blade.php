<script type="module">
    function showNotification(containerSelector, arMessages, type = 'success'){
        $(containerSelector).html('<div class="alert alert-' + type +'" role="alert">' + arMessages.join('<br>') + '</div>');
        setTimeout(function(){
            $(containerSelector).html('');
        },3000);
    }
</script>
