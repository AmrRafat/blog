$(function () {
  "use strict";
  // ============================
  // General Rules
  // ============================
  // Prevent resubmit of form upon refresh
  if (window.history.replaceState) {
    window.history.replaceState(null, null, window.location.href);
  }

  // $(window).on("keyup", function (e) {
  //   console.log(e.keyCode);
  // });
  // ============================
  // Login/Signup Rules
  // ============================
  // Showing signup form
  $(".signupBtn").on("click", function () {
    $(".loginForm").attr("style", "display:none;");
    $(".signupForm").addClass("active");
    $("#user").focus();
  });
  // Getting back to login form
  $(".back1").on("click", function () {
    $(".msg, .part1 i").attr("style", "display:none;");
    $("form.signupForm").trigger("reset");
    $(".signupForm").removeClass("active");
    $(".loginForm").attr("style", "display:block;");
  });
  // Getting to next info
  $(".next1").on("click", function () {
    if (
      $("#user").val() != "" &&
      $("#pass1").val() != "" &&
      $("#pass2").val() != ""
    ) {
      var user = $("#user").val();
      var pass1 = $("#pass1").val();
      var pass2 = $("#pass2").val();
      if (user.length >= 5 && user.length <= 16) {
        if (pass1 == pass2) {
          if (
            pass1.length >= 6 &&
            pass1.length <= 12 &&
            pass2.length >= 6 &&
            pass2.length <= 12
          ) {
            $(".part1").addClass("done");
            $(".part2").addClass("done");
            $("#fullname").focus();
          } else {
            $(".msg").html("Password must be 6-12 characters");
            $(".msg").fadeIn();
          }
        } else {
          $(".msg").html("Password is not matching");
          $(".msg").fadeIn();
        }
      } else {
        $(".msg").html("Username must be 5-16 letters");
        $(".msg").fadeIn();
      }
    } else {
      $(".msg").html("Please, fill all fields");
      $(".msg").fadeIn();
    }
  });
  // Remove error msgs upon any change
  $("#user,#pass1,#pass2").on("keyup change", function () {
    $(".msg").fadeOut();
  });
  // Getting back to first form of signup
  $(".back2").on("click", function () {
    $(".msg1").attr("style", "display:none;");
    $(".part2 input").val("");
    $(".part2").removeClass("done");
    $(".part1").removeClass("done");
  });
  // Check username availibility
  $("#user").on("keyup", function () {
    var username = $("#user").val();
    $.ajax({
      type: "POST",
      url: "includes/functions/ajax.php",
      data: {
        user: username,
      },
      success: function (html) {
        if (html == 0) {
          $("i.avail").fadeOut();
          $("i.inavail").fadeIn();
          $(".next1").prop("disabled", true);
        } else if (html == 1) {
          $("i.inavail").fadeOut();
          $("i.avail").fadeIn();
          $(".next1").prop("disabled", false);
        }
      },
    });
  });
  // Check second password matching
  $("#pass2").on("keyup", function () {
    var pass1 = $("#pass1").val();
    var pass2 = $("#pass2").val();
    if (pass1 == pass2) {
      $("i.diff").fadeOut();
      $("i.matching").fadeIn();
    } else {
      $("i.matching").fadeOut();
      $("i.diff").fadeIn();
    }
  });
  // Check second page of signup
  $("form.signupForm :submit").hover(function (e) {
    var fullName = $("#fullname").val();
    var email = $("#email").val();
    if (fullName == "" || email == "") {
      $("form.signupForm :submit").prop("disabled", true);
      $(".msg1").html("Please, fill all fields");
      $(".msg1").fadeIn();
    } else {
      $("form.signupForm :submit").prop("disabled", false);
    }
  });
  // Remove error msgs upon any change in second page
  $("#fullname, #email").on("keyup change", function () {
    $(".msg1").fadeOut();
  });
  // Prevent submit using enter
  $(".signupForm").on("keypress", function (e) {
    if (e.keyCode === 13 && e.target.nodeName != "TEXTAREA") {
      e.preventDefault();
    }
  });
  // ============================
  // Profile Rules
  // ============================
  // Open & submit profile edit
  $(".editProfile").on("click", function () {
    $(".editProfileForm input:not(.age, #specialization)").removeAttr(
      "readonly"
    );
    var eduLvl = $("#eduLvl").val();
    if (
      eduLvl == "Bachelor's degree" ||
      eduLvl == "Master's degree" ||
      eduLvl == "Doctorate or higher"
    ) {
      $(".editProfileForm input#specialization").removeAttr("readonly");
    }
    $(".editProfileForm select").removeAttr("disabled");
    $(".editProfile").addClass("saveEdits");
    $(".editProfile").html("Save");
    $(".editProfile").removeClass("editProfile");
    $(".saveEdits").on("click", function () {
      var user = $("#username").val();
      if (user.length >= 5 && user.length <= 16) {
        $(".saveEdits").html("Edit profile");
        $(".saveEdits").addClass("editProfile");
        $(".saveEdits").removeClass("saveEdits");
        $(".editProfileForm select").attr("disabled");
        $(".editProfileForm input").attr("readonly");
        $(".editProfileForm").trigger("submit");
      }
    });
  });
  // Check username
  $("#username").on("keyup", function () {
    var username = $("#username").val();
    if (username.length < 5) {
      if ($("i.avail").attr("style") === "display: inline;") {
        $("i.avail").fadeOut();
      }
      if ($("i.inavail").attr("style") === "display: inline;") {
        $("i.inavail").fadeOut();
      }
      $(".userLength").fadeIn();
    } else if ($(".userLength").attr("style") == "display: inline;") {
      $(".userLength").fadeOut();
    }
    if (username.length >= 5) {
      var id = $("#profileId").val();
      $.ajax({
        type: "POST",
        url: "includes/functions/ajax.php",
        data: {
          user: username,
          id: id,
        },
        success: function (html) {
          if (html == 0) {
            $("i.avail").fadeOut();
            $("i.inavail").fadeIn();
          } else if (html == 1) {
            $("i.inavail").fadeOut();
            $("i.avail").fadeIn();
          } else if (html == 2) {
            if ($("i.avail").attr("style") === "display: inline;") {
              $("i.avail").fadeOut();
            }
            if ($("i.inavail").attr("style") === "display: inline;") {
              $("i.inavail").fadeOut();
            }
          }
        },
      });
    }
  });
  // Make specializaiton field available upon selected the eudcational level
  $("#eduLvl").on("change", function () {
    var eduLvl = $(this).val();
    if (
      eduLvl == "Bachelor's degree" ||
      eduLvl == "Master's degree" ||
      eduLvl == "Doctorate or higher"
    ) {
      console.log("HI");
      $(".editProfileForm input#specialization").removeAttr("readonly");
    } else {
      console.log("not hi");
      $(".editProfileForm input#specialization").val("");
      $(".editProfileForm input#specialization").attr("readonly", "readonly");
    }
  });
  // ============================
  // Article Form
  // ============================
  // Allow changes to title
  $("h1.articleTitle").on("click", function () {
    var oldTitle = $("h1.articleTitle").text();
    $(".articleTitleGroup textarea").attr("placeholder", oldTitle);
    $("h1.articleTitle").attr("style", "display: none;");
    $(".articleTitleGroup").attr("style", "display: flex;");
    $(".articleTitleGroup textarea").focus();
  });
  $(".articleTitleDone").on("click", function () {
    var newTitle = $(".articleTitleGroup textarea").val();
    if (newTitle == "") {
      newTitle = "Article Title Here";
    }
    $("h1.articleTitle").text(newTitle);
    $("h1.articleTitle").attr("style", "display: block;");
    $(".articleTitleGroup").attr("style", "display: none;");
  });
  // Allow changes to subtitles
  $(document).on("click", ".articleForm h3", function () {
    var subtitle = $(this).text();
    $(this)
      .parent()
      .children(".subtitleChange")
      .attr("style", "display: flex;");
    $(this).parent().children(".subtitleChange").addClass("target");
    $(".target input").focus();
    $(".target input").attr("placeholder", subtitle);
    $(this).attr("style", "display: none;");
  });
  // Done changes to subtitles
  $(document).on("click", ".subtitleDone", function () {
    var newSubtitle = $(this).parent().children("input").val();
    if (newSubtitle == "") {
      newSubtitle = "Subtitle Here";
    }
    $(this).parent().parent().children("h3").text(newSubtitle);
    $(this).parent().parent().children("h3").attr("style", "display: block;");
    $(this).parent().attr("style", "display: none;");
    $(".target").removeClass("target");
  });
  // Allow changes to paragraphs
  $(document).on("click", ".articleForm p.paragraph", function () {
    var paragraph = $(this).text();
    $(this).parent().children(".pChange").attr("style", "display: flex;");
    $(this).parent().children(".pChange").addClass("target");
    $(".target textarea").focus();
    $(".target textarea").attr("placeholder", paragraph);
    $(this).attr("style", "display: none;");
  });
  // Done changes to paragraph
  $(document).on("click", ".pDone", function () {
    var newP = $(this).parent().children("textarea").val();
    if (newP == "") {
      newP = "Paragraph Here";
    }
    $(this)
      .parent()
      .parent()
      .children("p.paragraph")
      .html(newP.replace(/\r?\n/g, "<br/>"));
    $(this)
      .parent()
      .parent()
      .children("p.paragraph")
      .attr("style", "display: block;");
    $(this).parent().attr("style", "display: none;");
    $(".target").removeClass("target");
  });
  // img preview
  $(document).on("change", ".imgInput", function () {
    var file = $(this).get(0).files[0];
    if (file) {
      $(this).parent().parent().addClass("active");
      let reader = new FileReader();
      reader.onload = function () {
        $(".active img").attr("src", reader.result);
      };
      reader.readAsDataURL(file);
    }
    setTimeout(() => {
      $(this).parent().parent().removeClass("active");
    }, 1500);
  });
  // add blocks to article
  var subtitleNo = 1;
  var paragraphNo = 1;
  var imageNo = 1;
  // Subtitles
  $(".subtitleBtn").on("click", function () {
    $(".card-body").append(
      '<div class="mb-3"><h3 class="subtitle">Subtitle Here</h3><div class="input-group subtitleChange"><input type="text" name="subtitle-' +
        subtitleNo +
        '" class="form-control"><button type="button" class="btn btn-outline-secondary subtitleDone">Done</button><button type="button" class="btn btn-outline-secondary deleteBlock">Delete</button></div></div>'
    );
    subtitleNo++;
  });
  // Paragraphs
  $(".pBtn").on("click", function () {
    $(".card-body").append(
      '<div class="mb-3"><p class="paragraph" style="white-space:pre">Paragraph Here</p><div class="input-group pChange"><textarea name="paragraph-' +
        paragraphNo +
        '" cols="30" rows="10" class="form-control" style="resize: none;"></textarea><button type="button" class="btn btn-outline-secondary pDone">Done</button><button type="button" class="btn btn-outline-secondary deleteBlock">Delete</button></div></div>'
    );
    paragraphNo++;
  });
  // Images
  $(".imgBtn").on("click", function () {
    $(".card-body").append(
      '<div class="mb-3"><input type="hidden" name="img-' +
        imageNo +
        '" value="1"><img src="https://placehold.co/500x350" alt="" class="mx-auto d-block img-thumbnail img-fluid mb-2 articleImg"><div class="input-group justify-content-center"><input type="file" name="image-' +
        imageNo +
        '" class="btn btn-outline-secondary imgInput" accept=".png, .jpg" value="Choose Image"><button type="button" class="btn btn-outline-secondary deleteBlock">Delete</button></div></div>'
    );
    imageNo++;
  });
  // Allow delete a block added
  $(document).on("click", ".deleteBlock", function () {
    $(this).parent().parent().remove();
  });
  // Prevent Submit upon using Enter key
  $(".articleForm").on("keypress", function (e) {
    if (e.keyCode === 13 && e.target.nodeName != "TEXTAREA") {
      e.preventDefault();
    }
  });
  // Apply uploaded once a file choosen
  $('input[type="file"]').on("change", function () {
    if ($(this).val() != "") {
      $(this).addClass("filled");
    } else {
      $(this).removeClass("filled");
    }
  });
  // Edit comments
  $(".editComment").on("click", function () {
    $(".editCommentText").removeAttr("style", "display: block;");
    $(".editCommentText").attr("style", "display: none;");
    $(this)
      .parent()
      .parent()
      .parent()
      .parent()
      .children(".card-body")
      .children(".editCommentText")
      .attr("style", "display: block;");
    $(this)
      .parent()
      .parent()
      .parent()
      .parent()
      .children(".card-body")
      .children("p")
      .attr("style", "display: none;");
    $(".editComment").text("Save");
    setTimeout(() => {
      $(".editComment").addClass("save");
    }, 500);
    $(this)
      .parent()
      .parent()
      .parent()
      .parent()
      .children(".card-footer")
      .children("i")
      .addClass("rateChoice");
  });
  // Reset rating as well
  $("button[type=reset]").click(function () {
    $(this)
      .parent()
      .parent()
      .children(".rateField")
      .children("i")
      .addClass("fa-regular");
    $(this)
      .parent()
      .parent()
      .children(".rateField")
      .children("i")
      .removeClass("fa-solid");
  });
  // Save Changes
  $(document).on("click", ".save", function () {
    $(this).parent().parent().parent().parent().parent().trigger("submit");
  });
  // Delete Comment
  $(".delComment").on("click", function () {
    $(this)
      .parent()
      .parent()
      .parent()
      .parent()
      .children(".card-body")
      .children(".editCommentText")
      .text("DEL_COMMENT");
    setTimeout(() => {
      $(this).parent().parent().parent().parent().parent().trigger("submit");
    }, 100);
  });
  // Hover Rate
  $(document).on("mouseenter", ".rateChoice", function (e) {
    e.stopPropagation();
    $("i").removeClass("one");
    $("i").removeClass("two");
    $("i").removeClass("three");
    $("i").removeClass("four");
    $("i").removeClass("five");
    $(this).parent().children("i:nth-of-type(1)").addClass("one");
    $(this).parent().children("i:nth-of-type(2)").addClass("two");
    $(this).parent().children("i:nth-of-type(3)").addClass("three");
    $(this).parent().children("i:nth-of-type(4)").addClass("four");
    $(this).parent().children("i:nth-of-type(5)").addClass("five");
    if ($(this).hasClass("one")) {
      $(".rateChoice").addClass("fa-regular");
      $(".rateChoice").removeClass("fa-solid");
      $(this).parent().children(".one").removeClass("fa-regular");
      $(this).parent().children(".one").addClass("fa-solid");
      $(this).parent().children(".rate").val(1);
    } else if ($(this).hasClass("two")) {
      $(".rateChoice").removeClass("fa-solid");
      $(".rateChoice").addClass("fa-regular");
      $(this).parent().children(".one, .two").removeClass("fa-regular");
      $(this).parent().children(".one, .two").addClass("fa-solid");
      $(this).parent().children(".rate").val(2);
    } else if ($(this).hasClass("three")) {
      $(".rateChoice").removeClass("fa-solid");
      $(".rateChoice").addClass("fa-regular");
      $(this).parent().children(".one, .two, .three").removeClass("fa-regular");
      $(this).parent().children(".one, .two, .three").addClass("fa-solid");
      $(this).parent().children(".rate").val(3);
    } else if ($(this).hasClass("four")) {
      $(".rateChoice").removeClass("fa-solid");
      $(".rateChoice").addClass("fa-regular");
      $(this)
        .parent()
        .children(".one, .two, .three, .four")
        .removeClass("fa-regular");
      $(this)
        .parent()
        .children(".one, .two, .three, .four")
        .addClass("fa-solid");
      $(this).parent().children(".rate").val(4);
    } else if ($(this).hasClass("five")) {
      $(".rateChoice").removeClass("fa-solid");
      $(".rateChoice").addClass("fa-regular");
      $(this)
        .parent()
        .children(".one, .two, .three, .four, .five")
        .removeClass("fa-regular");
      $(this)
        .parent()
        .children(".one, .two, .three, .four, .five")
        .addClass("fa-solid");
      $(this).parent().children(".rate").val(5);
    }
  });
  // ============================
});
