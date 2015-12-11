var DZCP = {
    initDynLoader: function(tag) {
        $(function() { $('#mysql').load('index.php?action=mysql_setup_tb&ajax=1'); });
    },

    check: function(tag) {
        jQuery( "#" + tag ).button( "option", "disabled", document.getElementById('agb_checkbox').checked ? false : true );
    },

    enable: function(tag) {
        jQuery( "#" + tag ).button( "option", "disabled", false );
    }
};

$(function() {
    $("input[type=submit]" ).button().click(function( )
    {
        $(this).find("form").submit();
    });

    $("input[type=button]" ).button().click(function( )
    {
        $(this).find("form").submit();
    });
});