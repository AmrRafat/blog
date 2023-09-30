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
});
