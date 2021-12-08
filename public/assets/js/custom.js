//add more row
function addTableRow(tableID, tableRow, maxRow = null) {
    var clone_tr = $('#' + tableRow).clone(); // clone table row for multiple times
    var rowCount = $('#' + tableID).find('tr').length - 1; // minus 1 for thead tr
    var no_of_row = rowCount;
    var lastTr = $('#' + tableID).find('tr').last().attr('data-number');
    if (lastTr != '' && typeof lastTr !== "undefined") {
        rowCount = parseInt(lastTr) + 1;
    }
    // limit check
    if (maxRow != null && maxRow < no_of_row) {
        Swal.fire({
            title: 'Oops...',
            text: 'Exceed level cross!',
            icon: 'error'
        })
        return false;
    }
    // change attributes of new row
    var new_id = 'new' + tableID + rowCount;
    clone_tr.attr('id', new_id);
    clone_tr.attr('data-number', rowCount);
    clone_tr.attr('value', '');
    clone_tr.find('.custom-file-label').html('Choose product image'); // change label input type file
    clone_tr.find('input:radio').prop("checked", false); // uncheck new radio button when first radio is checked
    $("#" + tableID).append(clone_tr);

    //get input elements
    var attrInput = $("#" + tableID).find('#' + new_id).find('input');
    for (var i = 0; i < attrInput.length; i++) {
        var nameAtt = attrInput[i].name;
        var idAtt = attrInput[i].id;

        //set array element name & id
        var repName = nameAtt.replace('[0]', '[' + rowCount + ']');
        var repId = idAtt.replace('0', rowCount);
        attrInput[i].name = repName;
        attrInput[i].id = repId;

        if (attrInput[i].classList.contains('image')) {
            attrInput[i].setAttribute('data-placement', attrInput[0].getAttribute('data-placement') + rowCount);
            var dataTarget = attrInput[i].getAttribute('data-target-input');
            var dataTargetName = attrInput[i].getAttribute('data-target-input-name');
            attrInput[i].setAttribute('data-target-input', dataTarget.replace('[0]', '[' + rowCount + ']'));
            attrInput[i].setAttribute('data-target-input-name', dataTargetName.replace('[0]', '[' + rowCount + ']'));
        }

        if (repId.includes("image_")) { // for product image
            var base_url = window.location.origin + "/assets/img/boxed-bg.jpg";

            $("#" + tableID).find('#' + new_id).find('.preview_image').attr('src', base_url);
            $("#" + tableID).find('#' + new_id).find('.preview_image').attr('id', attrInput[0].getAttribute('data-placement'));
        } else if (repId.includes("is_thumbnail_")) { // for product thumbnail image
            $("#" + tableID).find('#' + new_id).find('.add-more-radio').attr('for', repId);
            $("#" + tableID).find('#' + repId).attr("data-image-sl", rowCount);
        }
    }
    attrInput.val(''); //value reset

    //get select box elements
    var attrSel = $("#" + tableID).find('#' + new_id).find('select');
    for (var i = 0; i < attrSel.length; i++) {
        var nameAtt = attrSel[i].name;
        var idAtt = attrSel[i].id;

        //set array element name & id
        var n = nameAtt.indexOf("[");
        var repName = nameAtt.replace(nameAtt.substring(n - 1, n), rowCount);
        var repId = idAtt.replace(nameAtt.substring(n - 1, n), rowCount);
        attrSel[i].name = repName;
        // attrSel[i].id = repId;
    }

    //create remove button
    $("#" + tableID).find('#' + new_id).find('.addTableRows').removeClass('btn-info').addClass('btn-danger')
        .attr('onclick', 'removeTableRow("' + tableID + '","' + new_id + '", "' + rowCount + '")');
    $("#" + tableID).find('#' + new_id).find('.addTableRows > .fa').removeClass('fa-plus').addClass('fa-times');
}

// Remove Table row script
function removeTableRow(tableID, removeId, trSL) {
    $('#' + tableID).find('#' + removeId).remove();

    // for product thumbnail image
    if (parseInt($('#thumbnail_image').val()) == parseInt(trSL)) {
        $('#thumbnail_image').val('');
    }
}

//preview image before upload
function imageUpload(input) {
    var img_preview_id = input.id + '_preview';
    if (input.files && input.files[0]) {
        //image type validation
        var mime_type = input.files[0].type;
        if (!(mime_type == 'image/jpeg' || mime_type == 'image/jpg' || mime_type == 'image/png')) {
            input.value = '';
            Swal.fire({
                title: 'Oops...',
                text: 'Invalid image format! Only JPEG or JPG or PNG image types are allowed.',
                icon: 'warning'
            })
            return false;
        }
        //image size validation
        var max_size = .5;
        var file_size = parseFloat(input.files[0].size / (1024 * 1024)).toFixed(1); // MB calculation
        if (file_size > max_size) {
            input.value = '';
            Swal.fire({
                title: 'Oops...',
                text: 'Max file size ' + (max_size * 1024) + ' KB. You have uploaded ' + file_size + ' MB.',
                icon: 'warning'
            })
            return false;
        }

        var reader = new FileReader();
        reader.onload = function (e) {
            $('#' + img_preview_id).attr('src', e.target.result);
        }
        $('#' + input.id + '_file_label').html(input.files[0].name);

        reader.readAsDataURL(input.files[0]); // convert to base64 string
    }
}