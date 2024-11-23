jQuery.validator.addMethod("notEqual", function (value, element, comparedelement) {
    if (value == $(comparedelement).val()) {
        return false;
    } else {
        return true;
    }
}, "This has to be different.");

$.validator.addMethod('minStrict', function (value, el, param) {
    return parseInt(value) > parseInt(param);
});

$.validator.addMethod('maxNumberLength', function (value, el, param) {
    return parseInt(value) < parseInt(param);
});

$.validator.addMethod('futureDateTime', function (value, el, param) {
    let CurrentDateTime = moment();
    let inputDateTime = moment(value, 'YYYY-MM-DDTHH:mm');

    if (inputDateTime < CurrentDateTime) {
        return false;
    }
    return true;
});



