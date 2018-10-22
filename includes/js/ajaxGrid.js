//Plugin made by Anup Nepal

(function ($) {
    $.fn.ajaxGrid = function (options) {

        return this.each(function () {
            var $table = $(this);
            var pageUl;

            var self = {
                initialize: function () {
                    self.createTable(0, options.pageSize, options.defaultSortExpression, options.defaultSortOrder, 1);

                    $table.on("refreshGrid", function (event, jsonParameters) {
                        if (options.filterData != null)
                            options.filterData = self.concatJson(options.filterData, jsonParameters);
                        else
                            options.filterData = jsonParameters;

                        self.createTable(0, options.pageSize, options.defaultSortExpression, options.defaultSortOrder, 1);
                    });

                    if (options.isSortable == null || options.isSortable) {

                        $table.find(options.tableHeading).parent('th').addClass('sorting');

                        $table.find(options.tableHeading).parent('th').bind("click", function () {
                            self.sorting(this, options.pageSize);
                        });
                    }

                    if (options.refreshEverySeconds != null) {
                        setInterval(function () {
                            self.createTable(0, options.pageSize, options.defaultSortExpression, options.defaultSortOrder, 1);
                        }, options.refreshEverySeconds * 1000);
                    }
                },
                compareObjectValue: function (object, valueToCompare) {

                    var matches = false;

                    for (var comp = 0; comp < object.length; comp++) {
                        if (object[comp].name == valueToCompare) {
                            matches = true;
                            break;
                        }
                    }

                    return matches;

                },
                createTable: function (offset, pageSize, sortExpression, sortOrder, pageNumber) {

                    if (options.hideControlDuringTableCreation != null)
                        options.hideControlDuringTableCreation.css("visibility", "hidden");

                    var parameter = {
                        'offset': offset,
                        'rowNumber': pageSize,
                        'sortExpression': sortExpression,
                        'sortOrder': sortOrder,
                        'pageNumber': pageNumber
                    };

                    if (options.filterData != null)
                        parameter = self.concatJson(parameter, options.filterData);

                    var parameterJson = null;

                    if (options.addParameterToRow != null) {
                        parameterJson = JSON.parse(parameter.param);
                    }
                    /*added by a21*/
                    if (options.typeOfContent == null)
                        options.typeOfContent = "application/x-www-form-urlencoded";
                    /*end of addition*/
                    $.ajax({
                        url: options.url,
                        type: options.requestType,
                        dataType: "json",
                        data: parameter,
                        contentType: options.typeOfContent,
                        beforeSend: function () {
                            options.loadingImage.css("visibility", "visible");
                        },
                        complete: function () {
                            options.loadingImage.css("visibility", "hidden");

                            if (options.afterAjaxCallComplete != null) {
                                options.afterAjaxCallComplete();
                            }

                            if (options.afterSelfCallComplete != null) {
                                options.afterSelfCallComplete($table);
                            }
                        },
                        success: function (data) {

                            $table.find('tbody').html('');

                            if (pageUl != null)
                                pageUl.remove();

                            if (options.linkedTable != null) {

                                var $linkedTable = options.linkedTable.table;

                                $linkedTable.find('tbody').html('');

                                $.each(data.Data, function (i, linkedItem) {
                                    if (i != $(data.Data).length - 1) {
                                        var $tr = $("<tr/>");

                                        $linkedTable.find('thead th').each(function () {
                                            var $td = $("<td/>");

                                            $td.html(linkedItem[$(this).find('span').attr('field-name')]);

                                            $tr.append($td);
                                        });

                                        $linkedTable.find('tbody').append($tr);
                                    }
                                });
                            }

                            var background = "#959595";
                            if (data.Header > 0) {
                                self.CreatingDataHeader(data.Data, parameterJson, background);
                            }
                            else {
                                self.CreatingDataRow(data.Data, parameterJson);
                            }


                            if (options.campaignStarted != null && options.campaignStarted == 1) {
                                $('.CampaignSelect').prop('disabled', true);
                                $('.CampaignContinue').prop('disabled', true);
                            }

                            self.createPaging(data.RowCount, pageNumber, options.pageSize);
                        }
                    });
                },
                CreatingDataHeader: function (Data, parameterJson, background) {
                    var noOfColumns = $table.find("tr:first th").length;
                    $.each(Data, function (headerIndex, headerValue) {

                        if (headerIndex >= 0) {
                            self.CreatingDataRow(Data, parameterJson);
                            return false;
                        } else {
                            var $tr = $("<tr/>");
                            var $td = $("<td colspan='" + noOfColumns + "'/>");
                            $td.css({
                                "text-align": "center",
                                "vertical-align": "middle",
                                "background-color": background
                            });
                            $td.append(headerIndex);
                            $tr.append($td);
                            $table.find('tbody').append($tr);
                            var background1 = "#D9D9D9";
                            self.CreatingDataHeader(headerValue, parameterJson, background1);
                        }

                    });
                },
                CreatingDataRow: function (rowData, parameterJson) {
                    var jsonMergeRowData = {};
                    $.each(rowData, function (dataIndex, item) {

                        var $tr = $("<tr/>");


                        if (parameterJson != null) {
                            $tr.attr(options.addParameterToRow, parameterJson[options.addParameterToRow]);
                        }

                        $tr.attr('id', item[options.id]);

                        $table.find('thead th').each(function () {

                            var addTd = [];

                            if (options.rowClass != null) {

                                if (options.rowClass.noCondition != null && options.rowClass.noCondition == true) {
                                    $tr.addClass(options.rowClass.class);
                                } else {
                                    if (item[options.rowClass.header] == options.rowClass.value) {
                                        $tr.addClass(options.rowClass.class);
                                    }
                                }
                            }

                            var $td = $("<td/>");

                            if (options.addClassToTd != null) {
                                if ($.inArray($(this).attr('hidden-data'), options.addClassToTd.fields) != -1) {
                                    $td.addClass(options.addClassToTd.class);
                                }
                            }

                            if ($(this).find('a').length != 0) {

                                if (options.contentAdditionalProperty != null && self.compareObjectValue(options.contentAdditionalProperty, $(this).find('a').attr('field-name'))) {

                                    var hasMergeContent = [];

                                    for (var cap = 0; cap < options.contentAdditionalProperty.length; cap++) {

                                        if ($(this).find('a').attr('field-name') == options.contentAdditionalProperty[cap].name) {

                                            if (options.contentAdditionalProperty[cap].mergeRepeatedName != null && options.contentAdditionalProperty[cap].mergeRepeatedName) {

                                                if (!$.isEmptyObject(jsonMergeRowData) && jsonMergeRowData.hasOwnProperty(options.contentAdditionalProperty[cap].name) &&
                                                    jsonMergeRowData[options.contentAdditionalProperty[cap].name].Value == item[options.contentAdditionalProperty[cap].mergeRespectTo]) {
                                                    addTd[options.contentAdditionalProperty[cap].name] = false;
                                                } else {
                                                    addTd[options.contentAdditionalProperty[cap].name] = true;
                                                }

                                                hasMergeContent[options.contentAdditionalProperty[cap].name] = true;

                                                var valueToCompare = rowData[dataIndex][options.contentAdditionalProperty[cap].mergeRespectTo];
                                                var startingIndex = dataIndex + 1;
                                                var offset = 1;

                                                for (i = startingIndex; i < rowData.length; i++) {
                                                    if (valueToCompare == rowData[i][options.contentAdditionalProperty[cap].mergeRespectTo]) {
                                                        offset++;
                                                    } else {
                                                        break;
                                                    }
                                                }

                                                jsonMergeRowData[options.contentAdditionalProperty[cap].name] = {
                                                    Value: valueToCompare,
                                                    Offset: offset
                                                };
                                            }
                                            if (options.contentAdditionalProperty[cap].name == $(this).find('a').attr('field-name')) {
                                                var additionalControl = options.contentAdditionalProperty[cap].control.clone();

                                                if (options.contentAdditionalProperty[cap].type != null && options.contentAdditionalProperty[cap].type == 'Text') {
                                                    for (k = 0; k < options.contentAdditionalProperty[cap].properties.length; k++) {
                                                        additionalControl.text(item[options.contentAdditionalProperty[cap].properties[k].text]);
                                                        additionalControl.attr('title', (item[options.contentAdditionalProperty[cap].properties[k].text]));
                                                    }
                                                } else if (options.contentAdditionalProperty[cap].type != null && options.contentAdditionalProperty[cap].type == 'Image') {
                                                    for (k = 0; k < options.contentAdditionalProperty[cap].properties.length; k++) {
                                                        if (options.contentAdditionalProperty[cap].properties[k].whenValue == item[options.contentAdditionalProperty[cap].name]) {
                                                            additionalControl.attr(options.contentAdditionalProperty[cap].properties[k].property, options.contentAdditionalProperty[cap].properties[k].value);
                                                        }
                                                    }
                                                }
                                                else {
                                                    for (k = 0; k < options.contentAdditionalProperty[cap].properties.length; k++) {
                                                        if (options.contentAdditionalProperty[cap].properties[k].type != null && options.contentAdditionalProperty[cap].properties[k].type == "Text") {
                                                            additionalControl.find(options.contentAdditionalProperty[cap].properties[k].field).text(item[options.contentAdditionalProperty[cap].properties[k].value]);
                                                        } else if (options.contentAdditionalProperty[cap].properties[k].type != null && options.contentAdditionalProperty[cap].properties[k].type == "Custom") {

                                                            if (options.contentAdditionalProperty[cap].properties[k].whenValue != null) {
                                                                if (options.contentAdditionalProperty[cap].properties[k].whenValue == item[options.contentAdditionalProperty[cap].properties[k].value]) {
                                                                    additionalControl.find(options.contentAdditionalProperty[cap].properties[k].field).attr(options.contentAdditionalProperty[cap].properties[k].property, item[options.contentAdditionalProperty[cap].properties[k].value]);
                                                                }
                                                            }
                                                            else {
                                                                additionalControl.find(options.contentAdditionalProperty[cap].properties[k].field).attr(options.contentAdditionalProperty[cap].properties[k].property, item[options.contentAdditionalProperty[cap].properties[k].value]);
                                                            }
                                                        } else if (options.contentAdditionalProperty[cap].properties[k].type != null && options.contentAdditionalProperty[cap].properties[k].type == "hidden") {

                                                            additionalControl.append('<input type="hidden"  value="' + item[options.contentAdditionalProperty[cap].hiddenValue] + '"/>');
                                                            additionalControl.append('<span>' + item[options.contentAdditionalProperty[cap].name] + '</span>');
                                                        }
                                                        else if (options.contentAdditionalProperty[cap].properties[k].type != null && options.contentAdditionalProperty[cap].properties[k].type == "link") {
                                                            if (options.contentAdditionalProperty[cap].linkSelectedData != null && options.contentAdditionalProperty[cap].linkSelectedData != "") {
                                                                if (options.contentAdditionalProperty[cap].compareSelectedData != null && item[options.contentAdditionalProperty[cap].compareSelectedData] == options.contentAdditionalProperty[cap].linkSelectedData) {

                                                                    if (item[options.contentAdditionalProperty[cap].name] == "") {
                                                                        additionalControl.append('<a href="#" target="_blank">' + item[options.contentAdditionalProperty[cap].name] + '</a>');
                                                                    }
                                                                    else {
                                                                        additionalControl.append('<a href= ' + item[options.contentAdditionalProperty[cap].name] + ' target="_blank">' + item[options.contentAdditionalProperty[cap].name] + '</a>');
                                                                    }
                                                                }
                                                                else {
                                                                    additionalControl.append('<span>' + item[options.contentAdditionalProperty[cap].name] + '</span>');
                                                                }
                                                            }
                                                        }
                                                        else {
                                                            additionalControl.find(options.contentAdditionalProperty[cap].properties[k].field).val(item[options.contentAdditionalProperty[cap].properties[k].value]);
                                                        }
                                                    }
                                                }

                                                if (options.contentAdditionalProperty[cap].replaceText != null) {
                                                    for (var rt = 0; rt < options.contentAdditionalProperty[cap].replaceText.length; rt++) {
                                                        if (options.contentAdditionalProperty[cap].replaceText[rt].field == 'self') {
                                                            additionalControl.attr(options.contentAdditionalProperty[cap].replaceText[rt].property,
                                                                additionalControl.attr(options.contentAdditionalProperty[cap].replaceText[rt].property).replace(options.contentAdditionalProperty[cap].replaceText[rt].textField,
                                                                    item[options.contentAdditionalProperty[cap].replaceText[rt].value].replace(' ', "")));
                                                        }
                                                    }
                                                }
                                                if (options.contentAdditionalProperty[cap].appendStyle != null && options.contentAdditionalProperty[cap].appendStyle) {

                                                    if (options.contentAdditionalProperty[cap].styleDataSource && options.contentAdditionalProperty[cap].styleDataSource == "database") {
                                                        for (k = 0; k < options.contentAdditionalProperty[cap].properties.length; k++) {

                                                            additionalControl.css(options.contentAdditionalProperty[cap].properties[k].cssType, (item[options.contentAdditionalProperty[cap].properties[k].Value]));
                                                        }
                                                    } else {
                                                        for (k = 0; k < options.contentAdditionalProperty[cap].properties.length; k++) {

                                                            additionalControl.css(options.contentAdditionalProperty[cap].properties[k].text, options.contentAdditionalProperty[cap].properties[k].Value);
                                                        }
                                                    }


                                                }
                                            }
                                            if (hasMergeContent.hasOwnProperty(options.contentAdditionalProperty[cap].name) && hasMergeContent[options.contentAdditionalProperty[cap].name]) {
                                                $td.attr('rowspan', jsonMergeRowData[options.contentAdditionalProperty[cap].name].Offset);
                                            }

                                        }
                                    }

                                    $td.html(additionalControl);

                                }
                                else if (options.appendProperty != null && self.compareObjectValue(options.appendProperty, $(this).find('a').attr('field-name'))) {
                                    var additionalControlAppend = '';
                                    for (var cap = 0; cap < options.appendProperty.length; cap++) {
                                        if (options.appendProperty[cap].name == $(this).find('a').attr('field-name')) {
                                            if (options.appendProperty[cap].property.whenValue == item[options.appendProperty[cap].property.field]) {
                                                additionalControlAppend += options.appendProperty[cap].control;
                                            }

                                        }

                                    }
                                    $td.html(item[$(this).find('a').attr('field-name')] + additionalControlAppend);
                                }
                                else {
                                    $td.html(item[$(this).find('a').attr('field-name')]);
                                }
                            }
                            else if ($(this).find('a').length == 0 && ((options.preContentType != null && options.preContentType == 'Multiple'
                                && $(this).index() < options.preContent.length) || $(this).index() == 0)) {

                                if (options.preContent != null) {

                                    var preControl;

                                    var i = 0;
                                    var count = 0;

                                    if (options.preContentType != null && options.preContentType == 'Multiple') {
                                        i = $(this).index();
                                        count = $(this).index() + 1;
                                    } else {
                                        count = options.preContent.length;
                                    }

                                    while (i < count) {

                                        var control = options.preContent[i].control.clone();

                                        if (options.preContent[i].properties != null) {

                                            for (var pt = 0; pt < options.preContent[i].properties.length; pt++) {
                                                if (options.preContent[i].properties[pt].propertyField == "this") {
                                                    if (options.preContent[i].properties[pt].setWhen != null) {

                                                        if (options.preContent[i].properties[pt].setWhen.relation == "equal") {
                                                            if (control.attr(options.preContent[i].properties[pt].setWhen.property) == item[options.preContent[i].properties[pt].setWhen.propertyValue])
                                                                control.attr(options.preContent[i].properties[pt].property, options.preContent[i].properties[pt].propertyValue);
                                                        } else {
                                                            if (control.attr(options.preContent[i].properties[pt].setWhen.property) != item[options.preContent[i].properties[pt].setWhen.propertyValue])
                                                                control.attr(options.preContent[i].properties[pt].property, options.preContent[i].properties[pt].propertyValue);
                                                        }

                                                    } else {
                                                        control.attr(options.preContent[i].properties[pt].property, item[options.preContent[i].properties[pt].propertyValue]);
                                                    }
                                                } else {
                                                    if (options.preContent[i].properties[pt].setWhen != null) {

                                                        if (options.preContent[i].properties[pt].setWhen.relation == "equal") {
                                                            if (control.attr(options.preContent[i].properties[pt].setWhen.property) == item[options.preContent[i].properties[pt].setWhen.propertyValue])
                                                                control.find(options.preContent[i].propertyField).attr(options.preContent[i].properties[pt].property,
                                                                    options.preContent[i].properties[pt].propertyValue);
                                                        } else {
                                                            if (control.attr(options.preContent[i].properties[pt].setWhen.property) != item[options.preContent[i].properties[pt].setWhen.propertyValue])
                                                                control.find(options.preContent[i].propertyField).attr(options.preContent[i].properties[pt].property,
                                                                    options.preContent[i].properties[pt].propertyValue);
                                                        }

                                                    } else {
                                                        control.find(options.preContent[i].properties[pt].propertyField).attr(options.preContent[i].properties[pt].property,
                                                            item[options.preContent[i].properties[pt].propertyValue]);
                                                    }
                                                }
                                            }
                                        }

                                        if (options.preContent[i].additionalControl != null) {

                                            for (j = 0; j < options.preContent[i].additionalControl.length; j++) {

                                                var additionalControl = options.preContent[i].additionalControl[j].control;

                                                if (options.preContent[i].additionalControl[j].displayedWhen.relation == "equal") {
                                                    if (item[options.preContent[i].additionalControl[j].displayedWhen.header] == options.preContent[i].additionalControl[j].displayedWhen.value) {

                                                        if (options.preContent[i].additionalControl[j].disabledWhen != null) {

                                                            var pauseContinueDisabled = false;
                                                            for (p = 0; p < options.preContent[i].additionalControl[j].disabledWhen.length; p++) {

                                                                if (options.preContent[i].additionalControl[j].disabledWhen[p].relation == 'equal' && !pauseContinueDisabled) {

                                                                    if ($.inArray(item[options.preContent[i].additionalControl[j].disabledWhen[p].header], options.preContent[i].additionalControl[j].disabledWhen[p].value) != -1) {
                                                                        pauseContinueDisabled = true;
                                                                    } else {
                                                                        pauseContinueDisabled = false;
                                                                    }
                                                                }
                                                                else if (options.preContent[i].additionalControl[j].disabledWhen[p].relation == 'not-equal' && !pauseContinueDisabled) {
                                                                    if ($.inArray(item[options.preContent[i].additionalControl[j].disabledWhen[p].header], options.preContent[i].additionalControl[j].disabledWhen[p].value) == -1) {
                                                                        pauseContinueDisabled = true;
                                                                    } else {
                                                                        pauseContinueDisabled = false;
                                                                    }
                                                                }
                                                                else if (options.preContent[i].additionalControl[j].disabledWhen[p].relation == 'lower-than' && !pauseContinueDisabled) {
                                                                    var dNow = new Date(options.preContent[i].additionalControl[j].disabledWhen[p].value);
                                                                    var $dateTill = new Date(dNow.getMonth() + 1 + '/' + dNow.getDate() + '/' + dNow.getFullYear() + " " + item[options.preContent[i].additionalControl[j].disabledWhen[p].header]);

                                                                    if ((Math.ceil((($dateTill - dNow) / 1000) / 60) <= 10))
                                                                        pauseContinueDisabled = true;
                                                                    else
                                                                        pauseContinueDisabled = false;
                                                                }
                                                            }
                                                            additionalControl.prop('disabled', pauseContinueDisabled);
                                                        }

                                                        control.append(additionalControl);

                                                        if (options.preContent[i].additionalControl[j].formAction != null) {
                                                            control.attr('action', options.preContent[i].additionalControl[j].formAction);
                                                        }
                                                    }
                                                } else if (options.preContent[i].additionalControl[j].displayedWhen.relation == "not-equal") {
                                                    if (item[options.preContent[i].additionalControl[j].displayedWhen.header] != options.preContent[i].additionalControl[j].displayedWhen.value) {

                                                        if (options.preContent[i].additionalControl[j].disabledWhen != null) {
                                                            var pauseContinueDisabled = false;
                                                            for (p = 0; p < options.preContent[i].additionalControl[j].disabledWhen.length; p++) {

                                                                if (options.preContent[i].additionalControl[j].disabledWhen[p].relation == 'equal' && !pauseContinueDisabled) {

                                                                    if ($.inArray(item[options.preContent[i].additionalControl[j].disabledWhen[p].header], options.preContent[i].additionalControl[j].disabledWhen[p].value) != -1) {
                                                                        pauseContinueDisabled = true;
                                                                    } else {
                                                                        pauseContinueDisabled = false;
                                                                    }
                                                                }
                                                                else if (options.preContent[i].additionalControl[j].disabledWhen[p].relation == 'not-equal' && !pauseContinueDisabled) {
                                                                    if ($.inArray(item[options.preContent[i].additionalControl[j].disabledWhen[p].header], options.preContent[i].additionalControl[j].disabledWhen[p].value) == -1) {
                                                                        pauseContinueDisabled = true;
                                                                    } else {
                                                                        pauseContinueDisabled = false;
                                                                    }
                                                                }
                                                                else if (options.preContent[i].additionalControl[j].disabledWhen[p].relation == 'lower-than' && !pauseContinueDisabled) {
                                                                    var dNow = new Date(options.preContent[i].additionalControl[j].disabledWhen[p].value);
                                                                    var $dateTill = new Date(dNow.getMonth() + 1 + '/' + dNow.getDate() + '/' + dNow.getFullYear() + " " + item[options.preContent[i].additionalControl[j].disabledWhen[p].header]);

                                                                    if ((Math.ceil((($dateTill - dNow) / 1000) / 60) <= 10))
                                                                        pauseContinueDisabled = true;
                                                                    else
                                                                        pauseContinueDisabled = false;
                                                                }
                                                            }
                                                            additionalControl.prop('disabled', pauseContinueDisabled);
                                                        }

                                                        control.append(additionalControl);

                                                        if (options.preContent[i].additionalControl[j].formAction != null) {
                                                            control.attr('action', options.preContent[i].additionalControl[j].formAction);
                                                        }
                                                    }
                                                }
                                            }
                                        }

                                        if (options.preContent[i].disabledWhen != null) {

                                            if (options.preContent[i].disabledWhen.relation == 'equal') {

                                                if ($.inArray(item[options.preContent[i].disabledWhen.header], options.preContent[i].disabledWhen.value) != -1) {
                                                    if (options.preContent[i].disabledWhen.propertyField == "this") {
                                                        control.prop('disabled', true)
                                                    } else {
                                                        control.find(options.preContent[i].disabledWhen.propertyField).prop('disabled', true);
                                                    }
                                                } else {
                                                    if (options.preContent[i].disabledWhen.propertyField == "this") {
                                                        control.prop('disabled', false)
                                                    } else {
                                                        control.find(options.preContent[i].disabledWhen.propertyField).prop('disabled', false);
                                                    }
                                                }
                                            } else {
                                                if ($.inArray(item[options.preContent[i].disabledWhen.header], options.preContent[i].disabledWhen.value) == -1) {
                                                    if (options.preContent[i].disabledWhen.propertyField == "this") {
                                                        control.prop('disabled', true)
                                                    } else {
                                                        control.find(options.preContent[i].disabledWhen.propertyField).prop('disabled', true);
                                                    }
                                                } else {
                                                    if (options.preContent[i].disabledWhen.propertyField == "this") {
                                                        control.prop('disabled', false)
                                                    } else {
                                                        control.find(options.preContent[i].disabledWhen.propertyField).prop('disabled', false);
                                                    }
                                                }
                                            }
                                        }

                                        if (options.preContentType != null && options.preContentType == 'Multiple') {
                                            preControl = control;
                                        }
                                        else {
                                            if (preControl == null)
                                                preControl = control;
                                            else {

                                                if (options.preContent[i].removeWhen == null)
                                                    preControl = preControl.add("<span> | </span>").add(control);
                                                else {
                                                    if (item[options.preContent[i].removeWhen.property] != options.preContent[i].removeWhen.value)
                                                        preControl = preControl.add("<span> | </span>").add(control);
                                                }
                                            }
                                        }

                                        i++;
                                    }

                                    if (preControl != null)
                                        $td.html(preControl.clone());
                                }
                            } else if ($(this).find('a').length == 0 && $(this).parent().find('th').length - 1 == $(this).index()) {

                                if (options.postContent != null) {

                                    var postControl;

                                    for (i = 0; i < options.postContent.length; i++) {

                                        var control = options.postContent[i].control.clone();

                                        if (options.postContent[i].properties != null) {

                                            for (var pt = 0; pt < options.postContent[i].properties.length; pt++) {

                                                if (options.postContent[i].properties[pt].propertyField == "this") {
                                                    if (options.postContent[i].properties[pt].setWhen != null) {

                                                        if (options.postContent[i].properties[pt].setWhen.relation == "equal") {
                                                            if (control.attr(options.postContent[i].properties[pt].setWhen.property) == item[options.postContent[i].properties[pt].setWhen.propertyValue])
                                                                control.attr(options.postContent[i].properties[pt].property, options.postContent[i].properties[pt].propertyValue);
                                                        } else {
                                                            if (control.attr(options.postContent[i].properties[pt].setWhen.property) != item[options.postContent[i].properties[pt].setWhen.propertyValue])
                                                                control.attr(options.postContent[i].properties[pt].property, options.postContent[i].properties[pt].propertyValue);
                                                        }

                                                    } else {
                                                        control.attr(options.postContent[i].properties[pt].property, item[options.postContent[i].properties[pt].propertyValue]);
                                                    }
                                                } else {
                                                    if (options.postContent[i].properties[pt].setWhen != null) {

                                                        if (options.postContent[i].properties[pt].setWhen.relation == "equal") {
                                                            if (control.attr(options.postContent[i].properties[pt].setWhen.property) == item[options.postContent[i].properties[pt].setWhen.propertyValue])
                                                                control.find(options.postContent[i].propertyField).attr(options.postContent[i].properties[pt].property,
                                                                    options.postContent[i].properties[pt].propertyValue);
                                                        } else {
                                                            if (control.attr(options.postContent[i].properties[pt].setWhen.property) != item[options.postContent[i].properties[pt].setWhen.propertyValue])
                                                                control.find(options.postContent[i].propertyField).attr(options.postContent[i].properties[pt].property,
                                                                    options.postContent[i].properties[pt].propertyValue);
                                                        }

                                                    } else {

                                                        if (options.postContent[i].properties[pt].propertyField == null) {
                                                            control.attr(options.postContent[i].properties[pt].property, item[options.postContent[i].properties[pt].propertyValue].replace(/ /g, '') + 'button');
                                                        }
                                                        else {
                                                            control.find(options.postContent[i].properties[pt].propertyField).attr(options.postContent[i].properties[pt].property,
                                                                item[options.postContent[i].properties[pt].propertyValue]);
                                                        }
                                                    }
                                                }
                                            }
                                        }

                                        if (options.postContent[i].additionalControl != null) {

                                            for (j = 0; j < options.postContent[i].additionalControl.length; j++) {

                                                var additionalControl = options.postContent[i].additionalControl[j].control;

                                                if (options.postContent[i].additionalControl[j].displayedWhen.relation == "equal") {
                                                    if (item[options.postContent[i].additionalControl[j].displayedWhen.header] == options.postContent[i].additionalControl[j].displayedWhen.value) {

                                                        if (options.postContent[i].additionalControl[j].disabledWhen != null) {

                                                            var pauseContinueDisabled = false;
                                                            for (p = 0; p < options.postContent[i].additionalControl[j].disabledWhen.length; p++) {

                                                                if (options.postContent[i].additionalControl[j].disabledWhen[p].relation == 'equal' && !pauseContinueDisabled) {

                                                                    if ($.inArray(item[options.postContent[i].additionalControl[j].disabledWhen[p].header], options.postContent[i].additionalControl[j].disabledWhen[p].value) != -1) {
                                                                        pauseContinueDisabled = true;
                                                                    } else {
                                                                        pauseContinueDisabled = false;
                                                                    }
                                                                }
                                                                else if (options.postContent[i].additionalControl[j].disabledWhen[p].relation == 'not-equal' && !pauseContinueDisabled) {
                                                                    if ($.inArray(item[options.postContent[i].additionalControl[j].disabledWhen[p].header], options.postContent[i].additionalControl[j].disabledWhen[p].value) == -1) {
                                                                        pauseContinueDisabled = true;
                                                                    } else {
                                                                        pauseContinueDisabled = false;
                                                                    }
                                                                }
                                                                else if (options.postContent[i].additionalControl[j].disabledWhen[p].relation == 'lower-than' && !pauseContinueDisabled) {
                                                                    var dNow = new Date(options.postContent[i].additionalControl[j].disabledWhen[p].value);
                                                                    var $dateTill = new Date(dNow.getMonth() + 1 + '/' + dNow.getDate() + '/' + dNow.getFullYear() + " " + item[options.postContent[i].additionalControl[j].disabledWhen[p].header]);

                                                                    if ((Math.ceil((($dateTill - dNow) / 1000) / 60) <= 10))
                                                                        pauseContinueDisabled = true;
                                                                    else
                                                                        pauseContinueDisabled = false;
                                                                }
                                                            }
                                                            additionalControl.prop('disabled', pauseContinueDisabled);
                                                        }

                                                        control.append(additionalControl);

                                                        if (options.postContent[i].additionalControl[j].formAction != null) {
                                                            control.attr('action', options.postContent[i].additionalControl[j].formAction);
                                                        }
                                                    }
                                                } else if (options.postContent[i].additionalControl[j].displayedWhen.relation == "not-equal") {
                                                    if (item[options.postContent[i].additionalControl[j].displayedWhen.header] != options.postContent[i].additionalControl[j].displayedWhen.value) {

                                                        if (options.postContent[i].additionalControl[j].disabledWhen != null) {
                                                            var pauseContinueDisabled = false;
                                                            for (p = 0; p < options.postContent[i].additionalControl[j].disabledWhen.length; p++) {

                                                                if (options.postContent[i].additionalControl[j].disabledWhen[p].relation == 'equal' && !pauseContinueDisabled) {

                                                                    if ($.inArray(item[options.postContent[i].additionalControl[j].disabledWhen[p].header], options.postContent[i].additionalControl[j].disabledWhen[p].value) != -1) {
                                                                        pauseContinueDisabled = true;
                                                                    } else {
                                                                        pauseContinueDisabled = false;
                                                                    }
                                                                }
                                                                else if (options.postContent[i].additionalControl[j].disabledWhen[p].relation == 'not-equal' && !pauseContinueDisabled) {
                                                                    if ($.inArray(item[options.postContent[i].additionalControl[j].disabledWhen[p].header], options.postContent[i].additionalControl[j].disabledWhen[p].value) == -1) {
                                                                        pauseContinueDisabled = true;
                                                                    } else {
                                                                        pauseContinueDisabled = false;
                                                                    }
                                                                }
                                                                else if (options.postContent[i].additionalControl[j].disabledWhen[p].relation == 'lower-than' && !pauseContinueDisabled) {
                                                                    var dNow = new Date(options.postContent[i].additionalControl[j].disabledWhen[p].value);
                                                                    var $dateTill = new Date(dNow.getMonth() + 1 + '/' + dNow.getDate() + '/' + dNow.getFullYear() + " " + item[options.postContent[i].additionalControl[j].disabledWhen[p].header]);

                                                                    if ((Math.ceil((($dateTill - dNow) / 1000) / 60) <= 10))
                                                                        pauseContinueDisabled = true;
                                                                    else
                                                                        pauseContinueDisabled = false;
                                                                }
                                                            }
                                                            additionalControl.prop('disabled', pauseContinueDisabled);
                                                        }

                                                        control.append(additionalControl);

                                                        if (options.postContent[i].additionalControl[j].formAction != null) {
                                                            control.attr('action', options.postContent[i].additionalControl[j].formAction);
                                                        }
                                                    }
                                                }
                                            }
                                        }

                                        if (options.postContent[i].disabledWhen != null) {

                                            if (options.postContent[i].disabledWhen.relation == 'equal') {

                                                if ($.inArray(item[options.postContent[i].disabledWhen.header], options.postContent[i].disabledWhen.value) != -1) {
                                                    control.find(options.postContent[i].disabledWhen.propertyField).prop('disabled', true);
                                                } else {
                                                    control.find(options.postContent[i].disabledWhen.propertyField).prop('disabled', false);
                                                }
                                            } else {
                                                if ($.inArray(item[options.postContent[i].disabledWhen.header], options.postContent[i].disabledWhen.value) == -1) {
                                                    control.find(options.postContent[i].disabledWhen.propertyField).prop('disabled', true);
                                                } else {
                                                    control.find(options.postContent[i].disabledWhen.propertyField).prop('disabled', false);
                                                }
                                            }
                                        }

                                        if (options.postContent[i].append != null) {
                                            control.attr(options.postContent[i].append.property,
                                                control.attr(options.postContent[i].append.property)
                                                + item[options.postContent[i].append.value] + options.postContent[i].append.text);
                                        }

                                        if (postControl == null) {
                                            if (options.postContent[i].removeWhen == null)
                                                postControl = control;
                                            else {

                                                var addControl = 1;

                                                if (options.postContent[i].removeWhen != null) {
                                                    $.each(options.postContent[i].removeWhen, function () {

                                                        if (item[this.property] >= this.value && this.relation == "greater") {
                                                            addControl = 0;
                                                        }

                                                        else if (item[this.property] == this.value && this.relation == "equal") {
                                                            addControl = 0;
                                                        }
                                                    });
                                                }

                                                if (addControl == 1) {
                                                    postControl = control;
                                                }
                                            }
                                        }
                                        else {
                                            var addControl = 1;
                                            if (options.postContent[i].removeWhen == null)
                                                addControl = 1;

                                            else {
                                                $.each(options.postContent[i].removeWhen, function () {

                                                    if (item[this.property] >= this.value && this.relation == "greater") {
                                                        addControl = 0;
                                                    }

                                                    else if (item[this.property] == this.value && this.relation == "equal") {
                                                        addControl = 0;
                                                    }
                                                });
                                            }
                                            if (addControl == 1) {
                                                postControl = postControl.add("<span> | </span>").add(control);
                                            }
                                        }
                                    }

                                    if (postControl != null)
                                        $td.html(postControl.clone());

                                }
                            }

                            if ($(this).find('a').length == 0) {
                                $tr.append($td);
                            } else if (!addTd.hasOwnProperty($(this).find('a').attr('field-name'))) {
                                $tr.append($td);
                            } else if (addTd.hasOwnProperty($(this).find('a').attr('field-name')) && addTd[$(this).find('a').attr('field-name')]) {
                                $tr.append($td);
                            }

                        });

                        if (options.rowClick != null && options.rowClick.rowClickAble) {
                            if (options.rowClick.excludeColumn != null) {

                                var tdToExclue = 'td:nth(' + options.rowClick.excludeColumn + ')';

                                $tr.find('td').not(tdToExclue).bind('click', function () {
                                    options.rowClick.rowClickFunction(this);
                                });
                            } else {
                                $tr.bind('click', function () {
                                    options.rowClick.rowClickFunction(this);
                                });
                            }
                        }

                        $table.find('tbody').append($tr);


                    });
                },
                createPaging: function (rowCount, pageNumber, pageSize) {

                    pageUl = $('<ul class="pagination pull-right"/>');

                    var pageRange;
                    var pagesToShow;
                    pagesToShow = 5;
                    var prvPages;
                    var postPages;
                    if (pagesToShow % 2 == 0) {
                        prvPages = (pagesToShow / 2) - 1;
                        postPages = (pagesToShow / 2);
                    }
                    else {
                        prvPages = parseInt(pagesToShow / 2);
                        postPages = prvPages;
                    }

                    if ((rowCount / options.pageSize) > 1) {
                        pageRange = Math.ceil(rowCount / options.pageSize)
                    } else {
                        pageRange = parseInt(rowCount / options.pageSize);
                    }

                    var pageNumberShowLower;
                    var pageNumberShowUpper;
                    var showFirstPage = false;
                    var showLastPage = false;

                    if (pageRange > pagesToShow) {
                        if (pageNumber < pagesToShow) {
                            pageNumberShowLower = 1;
                            pageNumberShowUpper = pagesToShow;
                            showLastPage = true;
                        }
                        else {
                            pageNumberShowLower = parseInt(pageNumber) - parseInt(prvPages);
                            pageNumberShowUpper = parseInt(pageNumber) + parseInt(postPages);
                            showFirstPage = true;
                            showLastPage = true;
                        }
                    }
                    else {
                        pageNumberShowLower = 1;
                        pageNumberShowUpper = pageRange;
                    }

                    if (pageNumberShowUpper >= pageRange) {
                        pageNumberShowUpper = pageRange;
                        showLastPage = false;
                    }
                    if (pageNumberShowLower - 1 == 1) {
                        pageNumberShowLower = 1;
                        pageNumberShowUpper = pagesToShow + 1;
                        showFirstPage = false;
                    }
                    if (pageNumberShowUpper + 1 == pageRange) {
                        pageNumberShowUpper = pageRange;
                        showLastPage = false;
                    }

                    var $pageLink;
                    var $pageLi;


                    for (var i = pageNumberShowLower; i <= pageNumberShowUpper; i++) {
                        $pageLi = $('<li/>');
                        $pageLink = $("<a/>");
                        $pageLink.attr('data-p', i);
                        $pageLink.addClass('page-button');
                        $pageLink.text(i);
                        if (i == pageNumber)
                            $pageLi.addClass('active');

                        $pageLi.append($pageLink);

                        pageUl.append($pageLi);

                        if (i == pageRange)
                            break;
                    }

                    if (showLastPage == true) {
                        $pageLi = $('<li/>');
                        $pageLink = $("<a class='dots'/>");
                        $pageLink.text('...');
                        $pageLi.append($pageLink);
                        pageUl.append($pageLi);

                        $pageLi = $('<li/>');
                        $pageLink = $("<a/>");
                        $pageLink.attr('data-p', pageRange);
                        $pageLink.addClass('page-button')
                        $pageLink.text(pageRange);
                        $pageLi.append($pageLink);
                        pageUl.append($pageLi);
                    }

                    if (showFirstPage == true) {
                        $pageLi = $('<li/>');
                        $pageLink = $("<a/>");
                        $pageLink.text('...');
                        $pageLi.prepend($pageLink);
                        pageUl.prepend($pageLi);

                        $pageLi = $('<li/>');
                        $pageLink = $("<a/>");
                        $pageLink.attr('data-p', 1);
                        $pageLink.addClass('page-button');
                        $pageLink.text(1);
                        $pageLi.prepend($pageLink);
                        pageUl.prepend($pageLi);
                    }

                    if (rowCount > 1) {
                        var $previousLi = $('<li/>');
                        var $previousPageLink = $("<a/>").text('Previous').addClass('previous');

                        if (pageNumber == 1) {
                            $previousLi.addClass('disabled');
                        }

                        $previousLi.append($previousPageLink);
                        pageUl.prepend($previousLi);
                    }

                    if (rowCount > 1) {
                        var $nextLi = $('<li/>');
                        var $nextPageLink = $("<a/>").text('Next').addClass('next');

                        if (pageNumber == pageNumberShowUpper) {
                            $nextLi.addClass('disabled');
                        }

                        $nextLi.append($nextPageLink);
                        pageUl.append($nextLi);
                    }

                    if (rowCount == 0) {

                        var $noRecordTr = $('<tr/>');

                        var $noRecordTd = $('<td/>').attr('colspan', $table.find('th').length);
                        $noRecordTd.text(options.NoRecordsFound);
                        $noRecordTr.append($noRecordTd);
                        $table.append($noRecordTr);

                    } else if (rowCount > options.pageSize) {
                        $table.after(pageUl);


                        pageUl.find('a').not('.previous').not('.next').not('.dots').on("click", function () {
                            self.changePage(this, pageSize, null, null);
                        });

                        pageUl.find('a.previous').on("click", function () {
                            self.changePage(this, pageSize, 'previous', pageNumber);
                        });

                        pageUl.find('a.next').on("click", function () {
                            self.changePage(this, pageSize, 'next', pageNumber);
                        });

                    }
                },
                concatJson: function extend(a, b) {
                    for (var key in b)
                        if (b.hasOwnProperty(key))
                            a[key] = b[key];
                    return a;
                },
                changePage: function (thisObj, pageSize, pagerType, pageNumber) {

                    if (pagerType == null) {
                        var sortExpression = options.defaultSortExpression;
                        var sortOrder = options.defaultSortOrder;

                        if ($table.find(options.tableHeading + '[sort-expression=asc]').length > 0) {
                            sortExpression = $table.find(options.tableHeading + '[sort-expression=asc]').attr('field-name');
                            sortOrder = 'asc';
                        }
                        else if ($table.find(options.tableHeading + '[sort-expression=desc]').length > 0) {
                            sortExpression = $table.find(options.tableHeading + '[sort-expression=desc]').attr('field-name');
                            sortOrder = 'desc';
                        }

                        var offset = 0;

                        if ($('.active').length > 0) {
                            offset = ($(thisObj).attr('data-p') - 1) * pageSize;
                            pageNumber = $(thisObj).attr('data-p');
                        }
                        self.createTable(offset, pageSize, sortExpression, sortOrder, pageNumber);
                    } else {

                        if (!$(thisObj).parent('li').hasClass('disabled')) {

                            var pageToTrigger = 1;

                            switch (pagerType) {
                                case 'previous':
                                    pageToTrigger = parseInt(pageNumber) - 1;
                                    break;
                                case 'next':
                                    pageToTrigger = parseInt(pageNumber) + 1;
                                    break;
                            }

                            pageUl.find('a[data-p="' + pageToTrigger + '"]').trigger('click');
                        }
                    }
                },
                sorting: function (thisObj, pageSize) {

                    var $sortableAnchor = $(thisObj).find('a');

                    $table.find(options.tableHeading).parent('th').not($sortableAnchor).addClass('sorting').removeClass('sorting-desc').removeClass('sorting-asc');

                    var sortOrder;

                    if ($sortableAnchor.attr('sort-expression') == 'desc') {
                        $sortableAnchor.attr('sort-expression', 'asc');
                        sortOrder = 'asc';
                    }
                    else if ($sortableAnchor.attr('sort-expression') == null || $sortableAnchor.attr('sort-expression') == 'asc') {
                        $sortableAnchor.attr('sort-expression', 'desc');
                        sortOrder = 'desc';
                    }

                    switch (sortOrder) {
                        case 'asc':
                            $sortableAnchor.parent('th').removeClass('sorting');
                            $sortableAnchor.parent('th').removeClass('sorting-desc');
                            $sortableAnchor.parent('th').addClass('sorting-asc');
                            break;
                        case 'desc':
                            $sortableAnchor.parent('th').removeClass('sorting');
                            $sortableAnchor.parent('th').removeClass('sorting-asc');
                            $sortableAnchor.parent('th').addClass('sorting-desc');
                            break;
                    }

                    var offset = 0;
                    var pageNumber = 1;

                    if (pageUl.find('.active').length > 0) {
                        offset = (pageUl.find('.active').find('a').attr('data-p') - 1) * pageSize;
                        pageNumber = pageUl.find('.active').find('a').attr('data-p');
                    }

                    self.createTable(offset, pageSize, $sortableAnchor.attr('field-name'), sortOrder, pageNumber);

                }
            };

            self.initialize();

        });

    };
})(jQuery);
