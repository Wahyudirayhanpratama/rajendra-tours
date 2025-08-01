//-----------------------------------------------------------------------
// Version:        2.1
// Template name:  Finapp - Wallet & Banking HTML Mobile Template
// Item URL :      https://themeforest.net/item/finapp-wallet-banking-html-mobile-template/25738217
// Author:         Bragher
// Author URL :    https://themeforest.net/user/bragher
//-----------------------------------------------------------------------


//-----------------------------------------------------------------------
// Template Settings
//-----------------------------------------------------------------------
const Finapp = {
    //-------------------------------------------------------------------
    // PWA Settings
    PWA: {
        enable: false, // Enable or disable PWA
    },
    //-------------------------------------------------------------------
    // Dark Mode Settings
    Dark_Mode: {
        default: false, // Set dark mode as main theme
        local_mode: { // Activate dark mode between certain times of the day
            enable: false, // Enable or disable local dark mode
            start_time: 20, // Start at 20:00
            end_time: 7, // End at 07:00
        },
        auto_detect: { // Auto detect user's preferences and activate dark mode
            enable: false,
        }
    },
    //-------------------------------------------------------------------
    // Right to Left (RTL) Settings
    RTL: {
        enable: false, // Enable or disable RTL Mode
    },
    //-------------------------------------------------------------------
    // Animations
    Animation: {
        goBack: false, // Go back page animation
    },
    //-------------------------------------------------------------------
    // Test Mode
    Test: {
        enable: true, // Enable or disable test mode
        word: "testmode", // The word that needs to be typed to activate test mode
        alert: true, // Enable or disable alert when test mode is activated
        alertMessage: "Test mode activated. Look at the developer console!" // Alert message
    }
    //-------------------------------------------------------------------
}
//-----------------------------------------------------------------------


//-----------------------------------------------------------------------
// Elements
//-----------------------------------------------------------------------
var pageBody = document.querySelector("body");
var appSidebar = document.getElementById("sidebarPanel")
var loader = document.getElementById('loader');
//-----------------------------------------------------------------------

document.addEventListener('DOMContentLoaded', function () {
    const modalElement = document.getElementById('actionSheetInsertLogin');
    if (modalElement) {
        const loginModal = new bootstrap.Modal(modalElement);

        if (typeof isGuest !== 'undefined' && isGuest) {
            document.querySelectorAll('.form-control').forEach(el => el.disabled = true);
            document.querySelectorAll('button[type="submit"]').forEach(el => el.disabled = true);
            loginModal.show();
        }
    }
});

//-----------------------------------------------------------------------
// Service Workers
//-----------------------------------------------------------------------
if (Finapp.PWA.enable) {
    if ('serviceWorker' in navigator) {
        navigator.serviceWorker.register('./assets/v3/js/__service-worker.js')
            .then(reg => console.log('service worker registered'))
            .catch(err => console.log('service worker not registered - there is an error.', err));
    }
}
//-----------------------------------------------------------------------


//-----------------------------------------------------------------------
// Page Loader with preload
//----------------------------------------------------------------------
if (loader) {
    setTimeout(() => {
        loader.setAttribute("style", "pointer-events: none; opacity: 0; transition: 0.2s ease-in-out;");
        setTimeout(() => {
            loader.setAttribute("style", "display: none;");
        }, 1000);
    }, 450);
}
//-----------------------------------------------------------------------


//-----------------------------------------------------------------------
// Go Back Animation
function goBackAnimation() {
    pageBody.classList.add("animationGoBack")
    setTimeout(() => {
        window.history.go(-1);
    }, 300);
}
// Go Back Button
var goBackButton = document.querySelectorAll(".goBack");
goBackButton.forEach(function (el) {
    el.addEventListener("click", function () {
        if (Finapp.Animation.goBack) {
            goBackAnimation();
        }
        else {
            window.history.go(-1);
        }

    })
})
//-----------------------------------------------------------------------


//-----------------------------------------------------------------------
// RTL (Right to Left)
if (Finapp.RTL.enable) {
    var pageHTML = document.querySelector("html")
    pageHTML.dir = "rtl"
    document.querySelector("body").classList.add("rtl-mode")
    if (appSidebar != null) {
        appSidebar.classList.remove("panelbox-left")
        appSidebar.classList.add("panelbox-right")
    }
    document.querySelectorAll(".carousel-full, .carousel-single, .carousel-multiple, .carousel-small, .carousel-slider").forEach(function (el) {
        el.setAttribute('data-splide', '{"direction":"rtl"}')
    })
}
//-----------------------------------------------------------------------


//-----------------------------------------------------------------------
// Tooltip
var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
    return new bootstrap.Tooltip(tooltipTriggerEl)
})
//-----------------------------------------------------------------------


//-----------------------------------------------------------------------
// Fix for # href
//-----------------------------------------------------------------------
var aWithHref = document.querySelectorAll('a[href*="#"]');
aWithHref.forEach(function (el) {
    el.addEventListener("click", function (e) {
        e.preventDefault();
    })
});
//-----------------------------------------------------------------------


//-----------------------------------------------------------------------
// Input
// Clear input
var clearInput = document.querySelectorAll(".clear-input");
clearInput.forEach(function (el) {
    el.addEventListener("click", function () {
        var parent = this.parentElement
        var input = parent.querySelector(".form-control")
        input.focus();
        input.value = "";
        parent.classList.remove("not-empty");
    })
})
// active
var formControl = document.querySelectorAll(".form-group .form-control");
formControl.forEach(function (el) {
    // active
    el.addEventListener("focus", () => {
        var parent = el.parentElement
        parent.classList.add("active")
    });
    el.addEventListener("blur", () => {
        var parent = el.parentElement
        parent.classList.remove("active")
    });
    // empty check
    el.addEventListener("keyup", log);
    function log(e) {
        var inputCheck = this.value.length;
        if (inputCheck > 0) {
            this.parentElement.classList.add("not-empty")
        }
        else {
            this.parentElement.classList.remove("not-empty")
        }
    }
})
//-----------------------------------------------------------------------


//-----------------------------------------------------------------------
// Searchbox Toggle
var searchboxToggle = document.querySelectorAll(".toggle-searchbox")
searchboxToggle.forEach(function (el) {
    el.addEventListener("click", function () {
        var search = document.getElementById("search")
        var a = search.classList.contains("show")
        if (a) {
            search.classList.remove("show")
        }
        else {
            search.classList.add("show")
            search.querySelector(".form-control").focus();
        }
    })
});
//-----------------------------------------------------------------------


//-----------------------------------------------------------------------
// Carousel
// Splide Carousel
document.addEventListener('DOMContentLoaded', function () {

    // Full Carousel
    document.querySelectorAll('.carousel-full').forEach(carousel => new Splide(carousel, {
        perPage: 1,
        rewind: true,
        type: "loop",
        gap: 0,
        arrows: false,
        pagination: false,
    }).mount());

    // Single Carousel
    document.querySelectorAll('.carousel-single').forEach(carousel => new Splide(carousel, {
        perPage: 3,
        rewind: true,
        type: "loop",
        gap: 16,
        padding: 16,
        arrows: false,
        pagination: false,
        breakpoints: {
            768: {
                perPage: 1
            },
            991: {
                perPage: 2
            }
        }
    }).mount());

    // Multiple Carousel
    document.querySelectorAll('.carousel-multiple').forEach(carousel => new Splide(carousel, {
        perPage: 4,
        rewind: true,
        type: "loop",
        gap: 16,
        padding: 16,
        arrows: false,
        pagination: false,
        breakpoints: {
            768: {
                perPage: 2
            },
            991: {
                perPage: 3
            }
        }
    }).mount());

    // Small Carousel
    document.querySelectorAll('.carousel-small').forEach(carousel => new Splide(carousel, {
        perPage: 9,
        rewind: false,
        type: "loop",
        gap: 16,
        padding: 16,
        arrows: false,
        pagination: false,
        breakpoints: {
            768: {
                perPage: 4
            },
            991: {
                perPage: 7
            }
        }
    }).mount());

    // Slider Carousel
    document.querySelectorAll('.carousel-slider').forEach(carousel => new Splide(carousel, {
        perPage: 1,
        rewind: false,
        type: "loop",
        gap: 16,
        padding: 16,
        arrows: false,
        pagination: true
    }).mount());

    // Stories Carousel
    document.querySelectorAll('.story-block').forEach(carousel => new Splide(carousel, {
        perPage: 16,
        rewind: false,
        type: "slide",
        gap: 16,
        padding: 16,
        arrows: false,
        pagination: false,
        breakpoints: {
            500: {
                perPage: 4
            },
            768: {
                perPage: 7
            },
            1200: {
                perPage: 11
            }
        }
    }).mount());
});
//-----------------------------------------------------------------------


//-----------------------------------------------------------------------
// Upload Input
var uploadComponent = document.querySelectorAll('.custom-file-upload');
uploadComponent.forEach(function (el) {
    var fileUploadParent = '#' + el.id;
    var fileInput = document.querySelector(fileUploadParent + ' input[type="file"]')
    var fileLabel = document.querySelector(fileUploadParent + ' label')
    var fileLabelText = document.querySelector(fileUploadParent + ' label span')
    var filelabelDefault = fileLabelText.innerHTML;
    fileInput.addEventListener('change', function (event) {
        var name = this.value.split('\\').pop()
        tmppath = URL.createObjectURL(event.target.files[0]);
        if (name) {
            fileLabel.classList.add('file-uploaded');
            fileLabel.style.backgroundImage = "url(" + tmppath + ")";
            fileLabelText.innerHTML = name;
        }
        else {
            fileLabel.classList.remove("file-uploaded")
            fileLabelText.innerHTML = filelabelDefault;
        }
    })
})
//-----------------------------------------------------------------------


//-----------------------------------------------------------------------
// Notification
// trigger notification
var notificationCloseButton = document.querySelectorAll(".notification-box .close-button");
var notificationTaptoClose = document.querySelectorAll(".tap-to-close .notification-dialog");
var notificationBox = document.querySelectorAll(".notification-box");

function closeNotificationBox() {
    notificationBox.forEach(function (el) {
        el.classList.remove("show")
    })
}
function notification(target, time) {
    var a = document.getElementById(target);
    closeNotificationBox()
    setTimeout(() => {
        a.classList.add("show")
    }, 250);
    if (time) {
        time = time + 250;
        setTimeout(() => {
            closeNotificationBox()
        }, time);
    }
}
// close notification
notificationCloseButton.forEach(function (el) {
    el.addEventListener("click", function (e) {
        e.preventDefault();
        closeNotificationBox();
    })
});

// tap to close notification
notificationTaptoClose.forEach(function (el) {
    el.addEventListener("click", function (e) {
        closeNotificationBox();
    })
});
//-----------------------------------------------------------------------


//-----------------------------------------------------------------------
// Toast
// trigger toast
var toastCloseButton = document.querySelectorAll(".toast-box .close-button");
var toastTaptoClose = document.querySelectorAll(".toast-box.tap-to-close");
var toastBoxes = document.querySelectorAll(".toast-box");

function closeToastBox() {
    toastBoxes.forEach(function (el) {
        el.classList.remove("show")
    })
}
function toastbox(target, time) {
    var a = document.getElementById(target);
    closeToastBox()
    setTimeout(() => {
        a.classList.add("show")
    }, 100);
    if (time) {
        time = time + 100;
        setTimeout(() => {
            closeToastBox()
        }, time);
    }
}
// close button toast
toastCloseButton.forEach(function (el) {
    el.addEventListener("click", function (e) {
        e.preventDefault();
        closeToastBox();
    })
})
// tap to close toast
toastTaptoClose.forEach(function (el) {
    el.addEventListener("click", function (e) {
        closeToastBox();
    })
})
//-----------------------------------------------------------------------


//-----------------------------------------------------------------------
// Add to Home
var osDetection = navigator.userAgent || navigator.vendor || window.opera;
var windowsPhoneDetection = /windows phone/i.test(osDetection);
var androidDetection = /android/i.test(osDetection);
var iosDetection = /iPad|iPhone|iPod/.test(osDetection) && !window.MSStream;

function iosAddtoHome() {
    var modal = new bootstrap.Modal(document.getElementById('ios-add-to-home-screen'))
    modal.toggle()
}
function androidAddtoHome() {
    var modal = new bootstrap.Modal(document.getElementById('android-add-to-home-screen'))
    modal.toggle()
}
function AddtoHome(time, once) {
    if (once) {
        var AddHomeStatus = localStorage.getItem("FinappAddtoHome");
        if (AddHomeStatus === "1" || AddHomeStatus === 1) {
            // already showed up
        }
        else {
            localStorage.setItem("FinappAddtoHome", 1)
            window.addEventListener('load', () => {
                if (navigator.standalone) {
                    // if app installed ios home screen
                }
                else if (matchMedia('(display-mode: standalone)').matches) {
                    // if app installed android home screen
                }
                else {
                    // if app is not installed
                    if (androidDetection) {
                        setTimeout(() => {
                            androidAddtoHome()
                        }, time);
                    }
                    if (iosDetection) {
                        setTimeout(() => {
                            iosAddtoHome()
                        }, time);
                    }
                }
            });
        }
    }
    else {
        window.addEventListener('load', () => {
            if (navigator.standalone) {
                // app loaded to ios
            }
            else if (matchMedia('(display-mode: standalone)').matches) {
                // app loaded to android
            }
            else {
                // app not loaded
                if (androidDetection) {
                    setTimeout(() => {
                        androidAddtoHome()
                    }, time);
                }
                if (iosDetection) {
                    setTimeout(() => {
                        iosAddtoHome()
                    }, time);
                }
            }
        });
    }

}
//-----------------------------------------------------------------------


//-----------------------------------------------------------------------
// Dark Mode
var checkDarkModeStatus = localStorage.getItem("FinappDarkmode");
var switchDarkMode = document.querySelectorAll(".dark-mode-switch");
var pageBodyActive = pageBody.classList.contains("dark-mode");

// Check if enable as default
if (Finapp.Dark_Mode.default) {
    pageBody.classList.add("dark-mode");
}

// Local Dark Mode
if (Finapp.Dark_Mode.local_mode.enable) {
    var nightStart = Finapp.Dark_Mode.local_mode.start_time;
    var nightEnd = Finapp.Dark_Mode.local_mode.end_time;
    var currentDate = new Date();
    var currentHour = currentDate.getHours();
    if (currentHour >= nightStart || currentHour < nightEnd) {
        // It is night time
        pageBody.classList.add("dark-mode");
    }
}

// Auto Detect Dark Mode
if (Finapp.Dark_Mode.auto_detect.enable)
    if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
        pageBody.classList.add("dark-mode");
    }

function switchDarkModeCheck(value) {
    switchDarkMode.forEach(function (el) {
        el.checked = value
    })
}
// if dark mode on
if (checkDarkModeStatus === 1 || checkDarkModeStatus === "1" || pageBody.classList.contains('dark-mode')) {
    switchDarkModeCheck(true);
    if (pageBodyActive) {
        // dark mode already activated
    }
    else {
        pageBody.classList.add("dark-mode")
    }
}
else {
    switchDarkModeCheck(false);
}
switchDarkMode.forEach(function (el) {
    el.addEventListener("click", function () {
        var darkmodeCheck = localStorage.getItem("FinappDarkmode");
        var bodyCheck = pageBody.classList.contains('dark-mode');
        if (darkmodeCheck === 1 || darkmodeCheck === "1" || bodyCheck) {
            pageBody.classList.remove("dark-mode");
            localStorage.setItem("FinappDarkmode", "0");
            switchDarkModeCheck(false);
        }
        else {
            pageBody.classList.add("dark-mode")
            switchDarkModeCheck(true);
            localStorage.setItem("FinappDarkmode", "1");
        }
    })
})
//-----------------------------------------------------------------------


//-----------------------------------------------------------------------
// Test Mode
function testMode() {
    var colorDanger = "color: #FF396F; font-weight:bold;"
    var colorSuccess = "color: #1DCC70; font-weight:bold;"

    console.clear();
    console.log("%cFINAPP", "font-size: 1.3em; font-weight: bold; color: #FFF; background-color: #6236FF; padding: 10px 120px; margin-bottom: 16px;")
    console.log("%cðŸš€ TEST MODE ACTIVATED ..!", "font-size: 1em; font-weight: bold; margin: 4px 0;");

    function testModeMsg(value, msg) {
        if (value) {
            console.log("%c|" + "%c " + msg + " : " + "%cEnabled", "color: #444; font-size :1.2em; font-weight: bold;", "color: inherit", colorSuccess);
        }
        else if (value == false) {
            console.log("%c|" + "%c " + msg + " : " + "%cDisabled", "color: #444; font-size :1.2em; font-weight: bold;", "color: inherit", colorDanger);
        }
    }
    function testModeInfo(value, msg) {
        console.log("%c|" + "%c " + msg + " : " + "%c" + value, "color: #444; font-size :1.2em; font-weight: bold;", "color: inherit", "color:#6236FF; font-weight: bold;");
    }
    function testModeSubtitle(msg) {
        console.log("%c # " + msg, "color: #FFF; background: #444; font-size: 1.2em; padding: 8px 16px; margin-top: 16px; border-radius: 12px 12px 0 0");
    }

    testModeSubtitle("THEME SETTINGS")
    testModeMsg(Finapp.PWA.enable, "PWA")
    testModeMsg(Finapp.Dark_Mode.default, "Set dark mode as default theme")
    testModeMsg(Finapp.Dark_Mode.local_mode.enable, "Local dark mode (between " + Finapp.Dark_Mode.local_mode.start_time + ":00 and " + Finapp.Dark_Mode.local_mode.end_time + ":00)")
    testModeMsg(Finapp.Dark_Mode.auto_detect.enable, "Auto detect dark mode")
    testModeMsg(Finapp.RTL.enable, "RTL")
    testModeMsg(Finapp.Test.enable, "Test mode")
    testModeMsg(Finapp.Test.alert, "Test mode alert")

    testModeSubtitle("PREVIEW INFOS")
    // Resolution
    testModeInfo(window.screen.availWidth + " x " + window.screen.availHeight, "Resolution")
    // Device
    if (iosDetection) {
        testModeInfo("iOS", "Device")
    }
    else if (androidDetection) {
        testModeInfo("Android", "Device")
    }
    else if (windowsPhoneDetection) {
        testModeInfo("Windows Phone", "Device")
    }
    else {
        testModeInfo("Not a Mobile Device", "Device")
    }
    //Language
    testModeInfo(window.navigator.language, "Language")
    // Theme
    if (pageBody.classList.contains("dark-mode")) {
        testModeInfo("Dark Mode", "Current theme")
    }
    else {
        testModeInfo("Light Mode", "Current theme")
    }
    // Online Status
    if (window.navigator.onLine) {
        testModeInfo("Online", "Internet connection")
    }
    else {
        testModeInfo("Offline", "Internet connection")
    }

    testModeSubtitle("ANIMATIONS")
    testModeMsg(Finapp.Animation.goBack, "Go Back")
}
function themeTesting() {
    var word = Finapp.Test.word;
    var value = "";
    window.addEventListener('keypress', function (e) {
        value = value + String.fromCharCode(e.keyCode).toLowerCase();
        if (value.length > word.length) {
            value = value.slice(1);
        }
        if (value == word || value === word) {
            value = ""
            if (Finapp.Test.alert) {
                var content = document.getElementById("appCapsule")
                content.appendChild(document.createElement("div")).className = "test-alert-wrapper";
                var alert =
                    "<div id='alert-toast' class='toast-box toast-center tap-to-close'>"
                    +
                    "<div class='in'>"
                    +
                    "<div class='text'><h1 class='text-light mb-05'>ðŸ¤–</h1><strong>"
                    +
                    Finapp.Test.alertMessage
                    +
                    "</strong></div></div></div>"
                var wrapper = document.querySelector(".test-alert-wrapper")
                wrapper.innerHTML = alert;
                toastbox('alert-toast');
                setTimeout(() => {
                    this.document.getElementById("alert-toast").classList.remove("show")
                }, 4000);
            }
            testMode();
        }

    })
}

if (Finapp.Test.enable) {
    themeTesting();
}
//-----------------------------------------------------------------------
