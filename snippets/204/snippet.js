
$(function(){
    var postActions   = $( '#list_PostActions' );
    var currentAction = $( '#list_PostActions li.active' );
    if ( currentAction.length === 0 ) {
        postActions.find( 'li:first' ).addClass( 'active' );
    }
    postActions.find( 'li' ).on( 'click', function( e ) {
        e.preventDefault();
        var self = $( this );
        if ( self === currentAction ) {return;}
        // else
        currentAction.removeClass( 'active' );
        self.addClass( 'active' );
        currentAction = self;
    });
});