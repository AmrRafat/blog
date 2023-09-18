$(function () {
  "use strict";
  // ============================
  // Login/Signup Rules
  // ============================
  // Showing signup form
  $(".signupBtn").on("click", function () {
    $(".loginForm").attr("style", "display:none;");
    $(".signupForm").addClass("active");
  });
  // Getting back to login form
  $(".back1").on("click", function () {
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
          } else {
            $(".msg").addClass("active");
            $(".msg").html("Password must be 6-12 characters");
          }
        } else {
          $(".msg").addClass("active");
          $(".msg").html("Password is not matching");
        }
      } else {
        $(".msg").addClass("active");
        $(".msg").html("Username must be 5-16 letters");
      }
    } else {
      $(".msg").addClass("active");
      $(".msg").html("Please, fill all fields");
    }
  });
  // Remove error msgs upon any change
  $("#user,#pass1,#pass2").on("keyup change", function () {
    $(".msg").removeClass("active");
  });
  // Getting back to first form of signup
  $(".back2").on("click", function () {
    $(".part2").removeClass("done");
    $(".part1").removeClass("done");
  });
  // ============================
});
