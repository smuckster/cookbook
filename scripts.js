// Global variable to keep track of number of ingredients
var ingredientNum = 1;
var stepNum = 1;
var noteNum = 0;
var refreshCount = 0;

$(document).ready(function() {
    // Automatically resize textareas and set them
    // to a default of 1 row initially
    $('#description').autoresize(1);
    $('.step').autoresize(1);
    $('.note').autoresize(1);

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

    // Handle the add new recipe form submit with AJAX
    $('#new-recipe-form').submit(function(e) {
        e.preventDefault();

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
                if(newdata.includes('id=')) {
                    window.location.replace('recipe.php?' + newdata);
                } else {
                    $('#form-error').text(newdata).show();
                }
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
        $('.user-nav-container i').toggleClass('rotate');
        $('.user-menu').slideToggle();
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
    var newRow = `<div class='ingredient-container' data-num='${ingredientNum}'><i class='fas fa-minus-circle' onclick='this.parentNode.remove()'></i><input type='text' class='ing-qty' id='ing-qty-${ingredientNum}' name='ing-qty[]' placeholder='#' onKeyUp='checkIngredientIfEmpty()'><input type='text' class='ing-unit' id='ing-unit-${ingredientNum}' name='ing-unit[]' placeholder='unit' onKeyUp='checkIngredientIfEmpty()'><input type='text' class='ing-name' id='ing-name-${ingredientNum}' name='ing-name[]' placeholder='ingredient' onKeyUp='checkIngredientIfEmpty()'></div>`;
    $('.ingredients-section').append(newRow);
}

function addIngredientHeading() {
    var headingPosition = $('.ingredients-section .ingredient-container').length;
    var newHeading = `<div class='ingredient-heading'><i class='fas fa-minus-circle' onclick='this.parentNode.remove()' style='margin-top: 1.6em'></i><input type='text' class='ing-heading' data-position='${headingPosition}' name='ing-headings[${headingPosition}]' placeholder='Heading' style='margin-top: 1.6em'>`;
    $('.ingredients-section').append(newHeading);
    addIngredientRow();
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
    }
}

function addProcessRow() {
    stepNum++;
    var newRow = `<div class='process-container' data-num='${stepNum}'><i class='fas fa-minus-circle' onclick='this.parentNode.remove()'></i><textarea class='step' id='step-${stepNum}' name='steps[]' placeholder='Next...' onKeyUp='checkProcessIfEmpty()'></textarea></div>`;
    $('.process-section').append(newRow);
    $('#step-' + stepNum).autoresize(1);
}

function addProcessHeading() {
    var headingPosition = $('.process-section .process-container').length;
    var newHeading = `<div class='process-heading'><i class='fas fa-minus-circle' onclick='this.parentNode.remove()' style='margin-top: 1.6em'></i><input type='text' class='proc-heading' data-position='${headingPosition}' name='proc-headings[${headingPosition}]' placeholder='Heading' style='margin-top: 1.6em'>`;
    $('.process-section').append(newHeading);
    addProcessRow();
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
    }
}

function addNoteRow() {
    noteNum++;
    var newRow = `<div class='notes-container' data-num='${noteNum}'><i class='fas fa-minus-circle' onclick='this.parentNode.remove()'></i><textarea class='note' id='note-${noteNum}' name='notes[]' placeholder='Remember to...' onKeyUp='checkNoteIfEmpty()'></textarea></div>`;
    $('.notes-section').append(newRow);
    $('#note-' + noteNum).autoresize(1);
}

function checkNoteIfEmpty() {
    var empty = 1;
    $('.notes-section .notes-container:last-child textarea').each(function() {
        if($(this).val()) {
            empty = 0;
        }
    });
    if(!empty) {
        addNoteRow();
    }
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

    /* Delete a tag from a recipe when the X is clicked
    $('.cat-tag .fa-times').click(function() {
        // Get current recipe's id
        var urlParams = getUrlParams();
        $.ajax({
            type: "POST",
            url: "process_categories.php",
            data: { categoryid: $(this).parent('.cat-tag').data('id'),
                    recipeid: urlParams['id'],
                    action: 'delete' },
            success: function(data) {
                if(data != "Failure") {
                    // Stop an infinite loop from happening because I'm
                    // not smart enough to handle it another way
                    if(refreshCount < 2) {
                        refresh_categories();
                        refreshCount++;
                    }                    
                }
            }
        });
    });*/

    // Stop click propagation inside categories div and process
    // all other click events within the div
    $('.categories-container').click(function(e) {
        /*
        // Add category to recipe when clicked
        $('.cat-list-item').click(function() {
            // Get current recipe's id
            var urlParams = getUrlParams();
            $.ajax({
                type: "POST",
                url: "process_categories.php",
                data: { categoryid: $(this).data('id'),
                        recipeid: urlParams['id'],
                        action: 'add' },
                success: function(data) {
                    if(data != "Failure") {
                        //$('.cats-applied').append(data);
                        refresh_categories();
                    }
                }
            });
        });

        // Add new category to database and recipe when clicked
        $('.color-container').click(function() {
            // Get current recipe's id
            var urlParams = getUrlParams();
            $.ajax({
                type: "POST",
                url: "process_categories.php",
                data: { recipeid: urlParams['id'],
                        categoryname: $('#cat-input').val(),
                        categorycolor: $(this).children('i').css('color'),
                        action: 'new' },
                success: function(data) {
                    if(data != "Failure") {
                        refresh_categories();
                    }
                }
            });
        });*/

        e.stopPropagation();
    });
}