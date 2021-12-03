(function() {
    $('.convo-messages').each(function(){
        let objDiv = this;
        objDiv.scrollTop = objDiv.scrollHeight;
    });
})(window);