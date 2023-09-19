$(function () {
  "use strict";
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
  $("#pass1").on("click, focus", function () {
    var username = $("#user").val();
    if (username == "") {
      $(".msg").html("There is no username");
      $(".msg").fadeIn();
    } else {
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
          } else if (html == 1) {
            $("i.inavail").fadeOut();
            $("i.avail").fadeIn();
          }
        },
      });
    }
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
  // ============================
});
