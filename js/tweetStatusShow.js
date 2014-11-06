    $(function() {
        JQTWEET = {            
            user: 'navalacademy', //username
            numTweets: 5, //number of tweets
            appendTo: '#usna-social-media-box',            
           
            loadTweets: function() {                
                $.ajax({
                    url: 'displayTimeline.php',
                    type: 'GET',
                    dataType: 'html',
                    //data: request,
                    success: function(data) {
                            try {
                                // append tweets into page                                                                                                                                              
                                    $(JQTWEET.appendTo).append(data);                                    
                            } catch(e) {
                                //item is less than item count
                            }
                    }
                });
            }
        };
    });