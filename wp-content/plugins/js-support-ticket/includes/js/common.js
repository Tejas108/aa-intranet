function fillSpaces(string) {
    string = string.replace(" ", "%20");
    return string;
}
function deleteCutomUploadedFile (field1) {
    jQuery("input#"+field1).val(1);
    jQuery("span."+field1).hide();
    
}