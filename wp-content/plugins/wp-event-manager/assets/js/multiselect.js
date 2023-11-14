var MultiSelect= function () {
    /// <summary>Constructor function of the event MultiSelect class.</summary>
    /// <returns type="Home" />      
    return {
     	///<summary>
        ///Initializes the multiselect.  
        ///</summary>     
        ///<returns type="initialization settings" />   
        /// <since>1.0.0</since>         
        init: function() 
        {
			Common.logInfo("MultiSelect.init...");  
			jQuery(".event-manager-multiselect").chosen({search_contains:!0});
		}   
    } //enf of returnmultiselect
}; //end of class

MultiSelect= MultiSelect();
jQuery(document).ready(function($) 
{
   MultiSelect.init();
});