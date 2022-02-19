// Global variable to keep track of number of ingredients
var ingredientNum = 1;
var stepNum = 1;
var noteNum = 0;
var refreshCount = 0;
var catEditingOn = 0;

$(document).ready(function() {
    /*$('.step').autoresize(1);
    $('.note').autoresize(1);*/

    /*$('.new-recipe-container textarea').each(function() {
        if($(this).text() != '') {
            $(this).autoresize(1);
        } else {
            $(this).autoresize(1);
        }
    });*/

    resizeTextareas();
    attachResizeListeners();

    // Hide username checker on new user form
    $('.username-match').hide();

    // Get current recipe's id
    var urlParams = getUrlParams();

    // Load in the categories on recipe pages
    refresh_categories(urlParams['id']);

    // Submit search form
    $('.submit-search').click(function() {
        $('#search-recipes').submit();
    });

    // Show hamburger menu when clicked
    $('.hamb-menu').click(function() {
        $('.hamb-menu-dropdown').slideToggle();
        $('.recipe-container').toggleClass('menu-open');
    });

    // Turn my category editing on
    $('.mycategories-edit').click(function() {
        $('.mycat-settings').slideToggle();
    });

    // Make toggle switches work for category privacy
    $('.toggle-switch-container').click(function() {
        var currentState = '';
        var currentCat = $(this);
        if($(this).children('.toggle-switch').hasClass('on')) {
            currentState = 'on';
        } else {
            currentState = 'off';
        }

        $.ajax({
            type: "POST",
            url: "process_categories.php",
            data: { categoryid: $(this).parents('.mycat-container').data('id'),
                    currentstate: currentState,
                    action: 'toggleprivacy'},
            success: function(data) {
                if(data == 'on') {
                    currentCat.children('.toggle-switch').removeClass('off').addClass('on');
                    currentCat.css('background-color', 'green');
                    currentCat.siblings('.public').css('color', '#000');
                    currentCat.siblings('.private').css('color', '#999');
                } else if(data == 'off') {
                    currentCat.children('.toggle-switch').removeClass('on').addClass('off');
                    currentCat.css('background-color', '#999999');
                    currentCat.siblings('.private').css('color', '#000');
                    currentCat.siblings('.public').css('color', '#999');
                }
            }
        });
    });

    // Make my categories actions work
    $('.mycategories-container .action-delete').click(function(e) {
        e.preventDefault();

        var categoryid = $(this).parents('.mycat-container').data('id');
        $(this).parents('.mycat-container').children('.delete-category').slideDown();
        $('.cancel-delete-button').click(function() {
            $(this).parent('.delete-category').slideUp();
        });
        $('.confirm-delete-button').click(function() {
            $.ajax({
                type: "POST",
                url: "process_categories.php",
                data: { action: 'purge',
                        categoryid: categoryid },
                success: function(data) {
                    if(data == "success") {
                        location.reload();
                    } else {
                        $(this).parent('.delete-category').html('Could not delete the category.');
                    }
                }
            });
        });
    });

    // Make my recipe actions work
    $('.action-edit').click(function(e) {
        e.preventDefault();

        var recipeid = $(this).parents('.result-container').data('id');
        window.location.replace('editrecipe.php?id=' + recipeid);
    });

    $('.myrecipes-container .action-delete').click(function(e) {
        e.preventDefault();

        var recipeid = $(this).parents('.result-container').data('id');
        $(this).parents('.result-container').next('.delete-recipe').slideDown();
        $('.cancel-delete-button').click(function() {
            $(this).parent('.delete-recipe').slideUp();
        });
        $('.confirm-delete-button').click(function() {
            $.ajax({
                type: "POST",
                url: "delete_recipe.php",
                data: { recipeid: recipeid },
                success: function(data) {
                    if(data == "success") {
                        location.reload();
                    } else {
                        $(this).parent('.delete-recipe').html('Could not delete the recipe.');
                    }
                }
            });
        });
    });

    // Show options for "Add..." button on editrecipe.php page
    $('.add-row').click(function() {
        $(this).children('.add-options').toggle(350);
    });

    // Edit category name on My Categories page
    $('.mycat-edit').click(function() {
        $(this).siblings('.mycat-name-edit').val($(this).siblings('.mycat-name').text());
        $(this).siblings('.mycat-name').toggle();
        $(this).siblings('.mycat-name-edit').toggle();
        $(this).siblings('.mycat-name-edit').focus();
    });

    $('.mycat-name-edit').keypress(function(e) {
        currentinput = $(this);
        var newname = $(this).val();
        var key = e.which;
        if(key == 13) {
            var catid = $(this).parent('.mycat-container').data('id');
            $.ajax({
                type: "POST",
                url: "process_categories.php",
                data: { categoryid: catid,
                        newname: newname,
                        action: 'edit' },
                success: function(data) {
                    if(data == 'Success') {
                        currentinput.siblings('.mycat-name').text(newname);
                        currentinput.toggle();
                        currentinput.siblings('.mycat-name').toggle();
                    }
                }
            });
        }
    });

    // Create recently added recipes carousel
    $('.recent-recipes-container').slick({
        focusOnSelect: true,
        arrows: true,
        infinite: true,
        speed: 300,
        slidesToShow: 4,
        slidesToScroll: 4
    });

    // Style the carousel buttons properly
    $('.slick-next').html('<i class="fas fa-chevron-right"></i>');
    $('.slick-prev').html('<i class="fas fa-chevron-left"></i>');

    // Hover effect for recipe cards
    $('.recent-recipe').hover(function() {
        $(this).find('.recent-img').css('transform', 'scale(1.07)');
    }, function() {
        $(this).find('.recent-img').css('transform', 'scale(1)');
    });

    // Hover effect for recipe cards
    $('.recipe-tile').hover(function() {
        $(this).find('.recent-img').css('transform', 'scale(1.07)');
    }, function() {
        $(this).find('.recent-img').css('transform', 'scale(1)');
    });

    // Handle the add new recipe form submit with AJAX
    $('#new-recipe-form').submit(function(e) {
        e.preventDefault();

        // Rename the form elements
        $('.ingredient-container').each(function() {

        });

        var form = $('#new-recipe-form')[0];
        var data = new FormData(form);

        $.ajax({
            type: "POST",
            enctype: 'multipart/form-data',
            url: "process_new_recipe.php",
            data: data,
            processData: false,
            contentType: false,
            cache: false,
            timeout: 800000,
            success: function(newdata) {
		    window.location.replace('recipe.php?' + newdata);
                /*if($('input[name="existingrecipe"]').val() != false) {
                    window.location.replace('recipe.php?id=' + $('input[name="existingrecipe"]').val());
                }
                else if(newdata.includes('id=')) {
                    //$('#form-error').text(newdata).show();
                    window.location.replace('recipe.php?' + newdata);
                } else {
                    $('#form-error').text(newdata).show();
                }*/
            },
            error: function(e) {
                $('#form-error').text(e.responseText).show();
            }
        });
    });

    // Check if passwords match on new user form
    $('.newuser-container input[type="password"]').keyup(function() {
        if($('#password').val() != '' || $('#password-verify').val() != '') {
            if($('#password').val() == $('#password-verify').val()) {
                $('.password-match .fa-times-circle').hide();
                $('.password-match .fa-check-square').show();
                $('.password-match').css({'border': '1px solid green',
                                         'background-color': 'lightgreen'});
            } else {
                $('.password-match .fa-check-square').hide();
                $('.password-match .fa-times-circle').show();
                $('.password-match').css({'border': '1px solid red',
                                         'background-color': '#ffcccc'});
            }
        }
    });

    // Submit new user form via AJAX
    $('.newuser-container form').submit(function(e) {
        e.preventDefault();

        // Make sure passwords match
        if($('#password').val() != $('#password-verify').val()) {
            $('#form-error').text('Your passwords must match!').show();
            return;
        }

        // Make sure username is unique
        $.ajax({
            type: "POST",
            url: "verifyusername.php",
            data: {"username": $('#username').val()},
            success: function(data) {
                if(data == "Taken") {
                    $('#form-error').text('Your username is already taken!').show();
                    return;
                } else {
                    $.ajax({
                        type: "POST",
                        url: "process_new_user.php",
                        data: $('.newuser-container form').serialize(),
                        success: function(data) {
                            if(data == 'Success') {
                                window.location.replace('index.php');
                            } else {
                                $('#form-error').text(data).show();
                            }
                        },
                        error: function(e) {
                            $('#form-error').text(e.responseText).show();
                        }
                    });
                }
            }
        });
    });

    // Check availability of username on new user form
    $('.newuser-container #username').keyup(function() {
        if($(this).val() != '') {
            $.ajax({
                type: "POST",
                url: "verifyusername.php",
                data: {"username": $('#username').val()},
                success: function(data) {
                    if(data == "Taken") {
                        $('.username-match span').text('Sorry, that username is already taken.');
                        $('.username-match .fa-times-circle').show();
                        $('.username-match .fa-check-square').hide();
                        $('.username-match').css({'border': '1px solid red',
                                            'background-color': '#ffcccc'});
                        $('.username-match').show();
                    } else {
                        $('.username-match span').text('Username is available!');
                        $('.username-match .fa-times-circle').hide();
                        $('.username-match .fa-check-square').show();
                        $('.username-match').css({'border': '1px solid green',
                                            'background-color': 'lightgreen'});
                        $('.username-match').show();
                    }
                }
            });
        }
    });

    // Expand user menu on click
    $('.user-nav-container').click(function() {
        $('.user-nav-container > i').toggleClass('rotate');
        $('.user-menu').slideToggle(100);
    });

    // Note button actions
    $('.cancel-note-button').click(function(){
        $(this).siblings('.note-textarea').val('');
        $(this).parents('.add-note-textarea-container').hide();
        $(this).parents('.add-note-textarea-container').siblings('.recipe-note').show();
    });

    $('.add-note-button').click(function(){
        var urlParams = getUrlParams();
        var recipeid = urlParams['id'];
        var element = $(this);
        $.ajax({
            type: "POST",
            url: "process_note.php",
            data: { action: 'add', 
                    recipeid: recipeid,
                    note: $(this).siblings('.note-textarea').val()},
            success: function(data) {
                element.siblings('.note-textarea').val('');
                element.parents('.add-note-textarea-container').hide();
                element.parents('.add-note-textarea-container').siblings('.recipe-note').show();
                element.parents('.add-recipe-note-container').siblings('.notes-container').append(data);
            }
        });
    });

    // Delete note button
    $('.r-note .action-delete').click(function() {
        var urlParams = getUrlParams();
        var recipeid = urlParams['id'];
        var element = $(this);
        $.ajax({
            type: "POST",
            url: "process_note.php",
            data: { action: 'delete',
                    noteid: element.parents('.r-note').data('id') },
            success: function(data) {
                if(data == 'success') {
                    element.parents('.r-note').hide();
                }
            }
        });
    });

    // Category tag control
    // -------------------------
    // Show the drop-down menu when the input it focused and
    // hide it when it is clicked outside of or when the X
    // icon is clicked.
    $('#cat-input').focus(function() {
        $('.cat-list').slideDown();
    });

    // Auto-filter category list results when typing
    $('#cat-input').keyup(function() {
        if($(this).val() != '') {
            $('.cat-list-item').each(function() {
                $(this).show();
                if(!$(this).text().toLowerCase().includes($('#cat-input').val())) {
                    $(this).hide();
                }
            });
        } else {
            $('.cat-list-item').show();
        }

        if($('.cat-list-item:visible').length == 0) {
            $('.cat-color-picker').show();
        } else {
            $('.cat-color-picker').hide();
        }
    });
    
});

function addIngredientRow() {
    ingredientNum++;
    var newRow = `<div class='ingredient-container' data-num='${ingredientNum}'><i class='fas fa-minus-circle' onclick='this.parentNode.remove()'></i><input type='text' class='ing-qty' id='ing-qty-${ingredientNum}' maxlength='5' name='ing-qty[]' placeholder='#' onKeyUp='checkIngredientIfEmpty()'><input type='text' class='ing-unit' id='ing-unit-${ingredientNum}' name='ing-unit[]' placeholder='unit' onKeyUp='checkIngredientIfEmpty()'><input type='text' class='ing-name' id='ing-name-${ingredientNum}' name='ing-name[]' placeholder='ingredient' onKeyUp='checkIngredientIfEmpty()'><div class='add-row'><a onclick='addIngredientBelow(this)'><i class='fas fa-plus-circle'></i>Add ingredient</a></div>`;
    $('.ingredients-section').append(newRow);
    attachResizeListeners();
}

function addIngredientBelow(element) {
    var newRow = `<div class='ingredient-container' data-num='${ingredientNum}'><i class='fas fa-minus-circle' onclick='this.parentNode.remove()'></i><input type='text' class='ing-qty' id='ing-qty-${ingredientNum}' maxlength='5' name='ing-qty[]' placeholder='#' onKeyUp='checkIngredientIfEmpty()'><input type='text' class='ing-unit' id='ing-unit-${ingredientNum}' name='ing-unit[]' placeholder='unit' onKeyUp='checkIngredientIfEmpty()'><input type='text' class='ing-name' id='ing-name-${ingredientNum}' name='ing-name[]' placeholder='ingredient' onKeyUp='checkIngredientIfEmpty()'><div class='add-row'><a onclick='addIngredientBelow(this)'><i class='fas fa-plus-circle'></i>Add ingredient</a></div>`;
    $(element).parents('.ingredient-container').after(newRow);
    attachResizeListeners();
}

function addIngredientHeading() {
    var headingPosition = $('.ingredients-section .ingredient-container').length - 1;
    var newHeading = `<div class='ingredient-heading'><i class='fas fa-minus-circle' onclick='this.parentNode.remove()' style='margin-top: 1.6em'></i><input type='text' class='ing-heading' data-position='${headingPosition}' name='ing-headings[${headingPosition}]' placeholder='Heading' style='margin-top: 1.6em'>`;
    $('.ingredients-section').append(newHeading);
    addIngredientRow();
    attachResizeListeners();
}

function checkIngredientIfEmpty() {
    var empty = 1;
    $('.ingredients-section .ingredient-container:last-child input[type="text"]').not('.ing-heading').each(function() {
        if($(this).val()) {
            empty = 0;
        }
    });
    if(empty == 0) {
        addIngredientRow();
        attachResizeListeners();
    }
}

function addProcessRow() {
    stepNum++;
    var newRow = `<div class='process-container' data-num='${stepNum}'><i class='fas fa-minus-circle' onclick='this.parentNode.remove()'></i><textarea class='step' id='step-${stepNum}' name='steps[]' placeholder='Next...' onKeyUp='checkProcessIfEmpty()' rows='1'></textarea><div class='add-row'><a onclick='addProcessBelow(this)'><i class='fas fa-plus-circle'></i>Add step</a></div></div>`;
    $('.process-section').append(newRow);
    //$('#step-' + stepNum).autoresize(1);
    attachResizeListeners();
}

function addProcessBelow(element) {
    stepNum++;
    var newRow = `<div class='process-container' data-num='${stepNum}'><i class='fas fa-minus-circle' onclick='this.parentNode.remove()'></i><textarea class='step' id='step-${stepNum}' name='steps[]' placeholder='Next...' onKeyUp='checkProcessIfEmpty()' rows='1'></textarea><div class='add-row'><a onclick='addProcessBelow(this)'><i class='fas fa-plus-circle'></i>Add step</a></div></div>`;
    $(element).parents('.process-container').after(newRow);
    //$('#step-' + stepNum).autoresize(1);
    attachResizeListeners();
}

function addProcessHeading() {
    var headingPosition = $('.process-section .process-container').length - 1;
    var newHeading = `<div class='process-heading'><i class='fas fa-minus-circle' onclick='this.parentNode.remove()' style='margin-top: 1.6em'></i><input type='text' class='proc-heading' data-position='${headingPosition}' name='proc-headings[${headingPosition}]' placeholder='Heading' style='margin-top: 1.6em'></div>`;
    $('.process-section').append(newHeading);
    addProcessRow();
    attachResizeListeners();
}

function checkProcessIfEmpty() {
    var empty = 1;
    $('.process-section .process-container:last-child textarea').each(function() {
        if($(this).val()) {
            empty = 0;
        }
    });
    if(empty == 0) {
        addProcessRow();
        attachResizeListeners();
    }
}

function addNoteRow() {
    noteNum++;
    var newRow = `<div class='notes-container' data-num='${noteNum}'><i class='fas fa-minus-circle' onclick='this.parentNode.remove()'></i><textarea class='note' data-id='${noteNum}' name='notes[]' placeholder='Remember to...' onKeyUp='checkNoteIfEmpty()' rows='1'></textarea></div>`;
    $('.notes-section').append(newRow);
    //$('#note-' + noteNum).autoresize(1);
    attachResizeListeners();
}

function checkNoteIfEmpty() {
    var empty = 1;
    /*$('.notes-section .notes-container:last-child textarea').each(function() {
        if($(this).val()) {
            empty = 0;
        }
    });*/
    if($('.notes-section .notes-container:last-child textarea').val()) {
        empty = 0;
    }

    if(empty == 0) {
        addNoteRow();
        attachResizeListeners();
    }
}

function addRecipeNote(element) {
    $(element).parents('.recipe-note').siblings('.add-note-textarea-container').show();
    $(element).parents('.recipe-note').hide();
    resizeTextareas();
    attachResizeListeners();
}

function getUrlParams() {
    var params = [], hash;
    var hashes = window.location.href.slice(window.location.href.indexOf('?') + 1).split('&');
    for(var i = 0; i < hashes.length; i++) {
        hash = hashes[i].split('=');
        params.push(hash[0]);
        params[hash[0]] = hash[1];
    }
    return params;
}

function refresh_categories() {
    // Get current recipe's id
    var urlParams = getUrlParams();
    recipeid = urlParams['id'];

    // Clear the category text input
    $('#cat-input').val('');

    // Refresh dropdown list with categories
    $.ajax({
        type: "POST",
        url: "process_categories.php",
        data: { recipeid: recipeid,
                action: 'allcategories' },
        success: function(data) {
            $('.cat-list').html(data);
            // Refresh applied categories for recipe
            $.ajax({
                type: "POST",
                url: "process_categories.php",
                data: { recipeid: recipeid,
                        action: 'recipecategories'},
                success: function(data) {
                    $('.cats-applied').html(data);
                    // Re-attach click events
                    click_events();
                }
            });
        }
    });
}

// Delete a tag from a recipe when the X is clicked
function delete_tag(element) {
    // Get current recipe's id
    var urlParams = getUrlParams();
    $.ajax({
        type: "POST",
        url: "process_categories.php",
        data: { categoryid: $(element).parent('.cat-tag').data('id'),
                recipeid: urlParams['id'],
                action: 'delete' },
        success: function(data) {
            if(data != "Failure") {
                refresh_categories();                    
            }
        }
    });
}

// Add category to recipe when clicked
function add_tag(element) {
    // Get current recipe's id
    var urlParams = getUrlParams();
    $.ajax({
        type: "POST",
        url: "process_categories.php",
        data: { categoryid: $(element).data('id'),
                recipeid: urlParams['id'],
                action: 'add' },
        success: function(data) {
            if(data != "Failure") {
                //$('.cats-applied').append(data);
                refresh_categories();
            }
        }
    });
}

// Add category to database when clicked
function new_tag(element) {
    // Get current recipe's id
    var urlParams = getUrlParams();
    $.ajax({
        type: "POST",
        url: "process_categories.php",
        data: { recipeid: urlParams['id'],
                categoryname: $('#cat-input').val(),
                color: $(element).css('color'),
                action: 'new' },
        success: function(data) {
            if(data != "Failure") {
                //$('.description').append(data);
                refresh_categories();
            }
        }
    });
}

function click_events() {
    $(document).click(function() {
        $('.cat-list').slideUp();
    });
    
    $('.categories-container .fa-times').click(function() {
        $('.cat-list').slideUp();
    });

    // Show editing categories interface when edit button clicked
    $('#edit-categories').click(function() {
        $('.categories-container').slideToggle();
        $('.cat-tag i').toggle();
        if($(this).children('span').text() == "Edit Categories") {
            $(this).children('span').text('Stop Editing');
            catEditingOn = 1;
        } else {
            $(this).children('span').text('Edit Categories');
            catEditingOn = 0;
        }
    });

    if(catEditingOn == 1) {
        $('.cat-tag i').toggle();
        $('#edit-categories span').text('Stop Editing');
    } else {
        $('#edit-categories span').text('Edit Categories');
    }

    // Hide edit categories button if user isn't logged in
    $('.unauthenticated #edit-categories').hide();

    // Stop click propagation inside categories div and process
    // all other click events within the div
    $('.categories-container').click(function(e) {
        e.stopPropagation();
    });
}

// Attach auto-resize event listeners to all textareas on the page
function attachResizeListeners() {
    $('textarea').keyup(function() {
        this.style.height = 'auto';
        this.style.height = (this.scrollHeight + 1) + 'px';
    });
}

// Manually resize all textareas on the page
function resizeTextareas() {
    $('textarea').each(function() {
        this.style.height = 'auto';
        this.style.height = (this.scrollHeight + 1) + 'px';
    });
}
