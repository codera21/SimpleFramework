function showAddNewForm(title, url, width, height, data) {
    var formDiv = $('<div></div>');
    if (data === "undefined") {
        data = null;
    }
    window.newFormDialog = formDiv.dialog({
        width: width,
        height: height,
        title: title,
        modal: true,
        close: function () {
            removeDialog(this);
        },
        open: function () {
        }
    });

    $.ajax({
        type: "post",
        url: url,
        data: {param: data},
        success: function (response) {
            formDiv.html(response);
        }
    });
}

function showEditForm(thisObj, title, url, width, height, data) {
    if ($(thisObj).closest('tr').length) {

        var id = $(thisObj).closest('tr').attr('id');

    } else {
        var id = $(thisObj).closest('div[data-id]').attr('data-id');
    }
    var formDiv = $('<div/>');
    formDiv.dialog({
        width: width,
        height: height,
        modal: true,
        title: title,
        position: 10,
        close: function () {
            removeDialog(this);
        },
        open: function () {
        },
        dialogClass: 'dlgfixed'
    });

    $.ajax({
        type: "get",
        url: url,
        data: {'ID': id, 'param': data},
        success: function (response) {
            formDiv.html(response);
        }
    });
}

function showPopUp(thisObj, title, url, width, height) {
    var datetime = $(thisObj).closest('tr').attr('id');

    var parameter = {'DateTime': datetime};

    if ($(thisObj).closest('tr').attr('Interval') != null) {
        parameter = extendJson(parameter, {'Interval': $(thisObj).closest('tr').attr('Interval')});
    }

    var formDiv = $('<div/>');
    window.newFormDialog = formDiv.dialog({
        width: width,
        height: height,
        title: title,
        close: function () {
            removeDialog(this);
        },
        open: function () {
        },
        dialogClass: 'dlgfixed'
    });

    $(".dlgfixed").center(false);

    $.ajax({
        type: "get",
        url: url,
        data: parameter,
        success: function (response) {
            formDiv.html(response);
        }
    });
}


function newFormSave(thisObj, url, title, width, height) {
    var formId = $(thisObj).attr("id");
    $('#' + formId).validationEngine();
    var alertDiv = $("<div/>");
    $.ajax({
        type: "POST",
        dataType: "json",
        url: "",
        data: $("#" + formId).serialize(),
        beforeSend: function () {
            $(alertDiv).html("please wait");
        },
        success: function (data) {
            $(alertDiv).html(data.message);
        }
    });
    var noticeDialog = alertDiv.dialog({
        width: width,
        height: height,
        title: title,
        buttons: {
            "Ok": function () {
                noticeDialog.dialog("close");
                newFormDialog.dialog("close");
                location.reload();
            }
        },
        close: function () {
            removeDialog(this);
        },
        open: function () {
        },
        dialogClass: 'dlgfixed'
    });
}
function removeDialog(thisObj) {
    $(thisObj).find('input').each(function () {
        $(this).trigger('removeErrorMessage');
    });

    $(thisObj).dialog('destroy').remove();
}

function closeDialog(thisObj) {
    $(thisObj).find('input').each(function () {
        $(this).trigger('removeErrorMessage');
    });

    $(thisObj).closest('.ui-dialog-content').dialog('close');

}


function AjaxConfirmation(methodCallBack, title, confirmMsg, OkLabel, CancelLabel) {

    if (OkLabel == null)
        OkLabel = "Yes";

    if (CancelLabel == null)
        CancelLabel = "No";

    var $confirmationDialogDiv = $("<div id='dialog-confirm' title='Confirm'><p>" + confirmMsg + "</p></div>");

    $confirmationDialogDiv.dialog({
        title: title,
        resizable: false,
        height: 'auto',
        width: 'auto',
        modal: true,
        open: function () {
        },
        buttons: [
            {
                text: OkLabel,
                click: function () {
                    $(this).dialog("close");
                    eval(methodCallBack);
                    $('#dialog-confirm').remove();
                }
            },
            {
                text: CancelLabel,
                click: function () {
                    $(this).dialog("close");
                    $('#dialog-confirm').remove();
                }
            }]
    }).parent().find(".ui-dialog-titlebar-close").click(function () {
        $('#dialog-confirm').remove();
    });
}

var confirmed = false;

function Confirmation(obj, title, confirmMsg, OkLabel, CancelLabel) {

    if (OkLabel == null)
        OkLabel = "Yes";

    if (CancelLabel == null)
        CancelLabel = "No";


    if (!confirmed) {
        $('body').append("<div id='dialog-confirm'><p>" + confirmMsg + "</p>");

        $("#dialog-confirm").dialog({
            title: title,
            resizable: false,
            height: "auto",
            width: "auto",
            open: function () {
            },
            modal: true,
            buttons: [
                {
                    text: OkLabel,
                    click: function () {
                        $(this).dialog("close");
                        $('#dialog-confirm').remove();
                        confirmed = true;
                        obj.click();
                    }
                },
                {
                    text: CancelLabel,
                    click: function () {
                        $(this).dialog("close");
                        $('#dialog-confirm').remove();
                    }
                }
            ]

        }).parent().find(".ui-dialog-titlebar-close").click(function () {
            $('#dialog-confirm').remove();
        });
    }
    return confirmed;
}

Date.prototype.addDays = function (days) {
    this.setDate(this.getDate() + days);
    return this;
};

Date.prototype.minusDays = function (days) {
    this.setDate(this.getDate() - days);
    return this;
};

Date.prototype.YmdFormat = function () {
    var yyyy = this.getFullYear().toString();
    var mm = (this.getMonth() + 1).toString(); // getMonth() is zero-based
    var dd = this.getDate().toString();

    return yyyy + '-' + (mm[1] ? mm : "0" + mm[0]) + '-' + (dd[1] ? dd : "0" + dd[0]); // padding
};


function DisplayPercentBar(tableId, columnIndex, maxWidth) {
    $("#" + tableId + " tbody tr td:nth-child(" + columnIndex + ")").each(function () {
        var cell = $(this);
        var actualContent = cell.text();

        if (actualContent == "")
            actualContent = "0.00";

        var percent = parseFloat(actualContent.replace('%', '').trim());
        var width = 1;
        if (!isNaN(percent) && percent > 0) {
            var temp = percent;
            if (temp == 0.00)
                width = 1;
            else
                width = (Math.round(maxWidth * temp) / 100).toFixed(1);
        }
        // cell.html("<div style='display: inline-block;width:" + width + "px;background-color:#3fbb32;height:10px;'>" + actualContent + "</div>");
        var element = $("#AnimationBar").clone();
        cell.html(element);
        element.animate({"width": width}, 1000);
        element.after("<span>  " + actualContent + "%</span>");
    });
}

function extendJson(a, b) {
    for (var key in b)
        if (b.hasOwnProperty(key))
            a[key] = b[key];
    return a;
}

function changeDialogHeight($element) {
    var uiDialog = $element.closest('.ui-dialog');
    uiDialog.css({
        'height': uiDialog.find('.ui-dialog-content').height() + 42,
        'top': '0',
        'left': '0',
        'right': '0',
        'bottom': '0',
        'margin': 'auto'
    });
}

function Warning(title, message) {
    var $WarningDialogDiv = $("<div id='dialog-confirm' title='Warning'><p>" + message + "</p></div>");
    $WarningDialogDiv.dialog({
        title: title,
        resizable: false,
        height: 'auto',
        width: 'auto',
        modal: true,
        buttons: {
            Ok: function () {
                $(this).dialog("close");
            }
        }
    });
}
/**
 * Created by dell on 10/12/2016.
 */
