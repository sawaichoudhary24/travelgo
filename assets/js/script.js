/* =========================================================
   TRAVELGO
   MOBILE MENU + GSAP + SCROLLTRIGGER + CURSOR BALL
   ========================================================= */


/* =========================================================
   MOBILE MENU
   ========================================================= */

const mobileMenuBtn = document.getElementById("mobileMenuBtn");
const mobileMenu = document.getElementById("mobileMenu");

if (mobileMenuBtn && mobileMenu) {

    mobileMenuBtn.addEventListener("click", () => {

        mobileMenu.classList.toggle("hidden");

    });

}


/* =========================================================
   DOM READY
   ========================================================= */

document.addEventListener("DOMContentLoaded", () => {

    /* GSAP check */
    if (typeof gsap === "undefined") {
        console.warn("GSAP library not loaded.");
        return;
    }

    /* ScrollTrigger check */
    if (typeof ScrollTrigger === "undefined") {
        console.warn("ScrollTrigger library not loaded.");
        return;
    }


    /* Register ScrollTrigger */
    gsap.registerPlugin(ScrollTrigger);


    /* =====================================================
       VARIABLES
       ===================================================== */

    const ball = document.querySelector(".cursor-ball");

    const header =
        document.querySelector("header") ||
        document.querySelector(".navbar") ||
        document.querySelector("nav");

    const reduceMotion =
        window.matchMedia(
            "(prefers-reduced-motion: reduce)"
        ).matches;


    /* =====================================================
       CUSTOM CURSOR BALL
       ===================================================== */

    if (
        ball &&
        !reduceMotion &&
        window.innerWidth > 768
    ) {

        /* -----------------------------------------------
           INITIAL BALL POSITION
           ----------------------------------------------- */

        gsap.set(ball, {
            x: window.innerWidth / 2,
            y: window.innerHeight / 2,
            scale: 1,
            rotation: 0,
            borderRadius: "50%",
            backgroundColor: "#2563eb"
        });


        /* -----------------------------------------------
           SMOOTH MOUSE MOVEMENT
           ----------------------------------------------- */

        const moveX = gsap.quickTo(ball, "x", {
            duration: 0.20,
            ease: "power3.out"
        });

        const moveY = gsap.quickTo(ball, "y", {
            duration: 0.20,
            ease: "power3.out"
        });


        window.addEventListener(
            "mousemove",
            (e) => {

                moveX(e.clientX);
                moveY(e.clientY);

            },
            {
                passive: true
            }
        );


        /* =================================================
           HEADER PAR BALL HIDE
           ================================================= */

        if (header) {

            header.addEventListener(
                "mouseenter",
                () => {

                    ball.classList.add(
                        "header-hidden"
                    );

                    ball.classList.remove(
                        "is-hover"
                    );

                }
            );


            header.addEventListener(
                "mouseleave",
                () => {

                    ball.classList.remove(
                        "header-hidden"
                    );

                }
            );

        }


        /* =================================================
           SCROLLTRIGGER BALL
           
           IMPORTANT:
           Ball ka color same BLUE rahega.
           Shape hamesha CIRCLE rahega.
           Sirf halka scale/rotation change hoga.
           ================================================= */

        const ballTimeline = gsap.timeline({

            scrollTrigger: {

                trigger: document.documentElement,

                start: "top top",

                end: "bottom bottom",

                scrub: 0.8

            }

        });


        /* -----------------------------------------------
           SCROLL PART 1
           ----------------------------------------------- */

        ballTimeline.to(ball, {

            scale: 1.25,

            rotation: 180,

            borderRadius: "50%",

            backgroundColor: "#2563eb",

            boxShadow:
                "0 0 0 4px rgba(37,99,235,.10), 0 0 20px rgba(37,99,235,.50)",

            ease: "none"

        });


        /* -----------------------------------------------
           SCROLL PART 2
           ----------------------------------------------- */

        ballTimeline.to(ball, {

            scale: 1.15,

            rotation: 360,

            borderRadius: "50%",

            backgroundColor: "#2563eb",

            boxShadow:
                "0 0 0 4px rgba(37,99,235,.10), 0 0 22px rgba(37,99,235,.52)",

            ease: "none"

        });


        /* -----------------------------------------------
           SCROLL PART 3
           ----------------------------------------------- */

        ballTimeline.to(ball, {

            scale: 1.3,

            rotation: 540,

            borderRadius: "50%",

            backgroundColor: "#2563eb",

            boxShadow:
                "0 0 0 5px rgba(37,99,235,.10), 0 0 24px rgba(37,99,235,.55)",

            ease: "none"

        });


        /* -----------------------------------------------
           SCROLL END
           ----------------------------------------------- */

        ballTimeline.to(ball, {

            scale: 1,

            rotation: 720,

            borderRadius: "50%",

            backgroundColor: "#2563eb",

            boxShadow:
                "0 0 0 2px rgba(37,99,235,.18), 0 0 12px rgba(37,99,235,.55), 0 0 25px rgba(37,99,235,.35)",

            ease: "none"

        });


        /* =================================================
           BUTTON / LINK HOVER
           ================================================= */

        document
            .querySelectorAll(
                "main a, main button, main select, main input"
            )
            .forEach((element) => {


                element.addEventListener(
                    "mouseenter",
                    () => {

                        if (
                            !ball.classList.contains(
                                "header-hidden"
                            )
                        ) {

                            ball.classList.add(
                                "is-hover"
                            );

                        }

                    }
                );


                element.addEventListener(
                    "mouseleave",
                    () => {

                        ball.classList.remove(
                            "is-hover"
                        );

                    }
                );

            });

    }


    /* =====================================================
       REDUCED MOTION
       ===================================================== */

    if (reduceMotion) {

        ScrollTrigger.refresh();

        return;

    }


    /* =====================================================
       SECTION REVEAL
       ===================================================== */

    gsap.utils
        .toArray(
            "main section, body > section"
        )
        .forEach((section) => {


            /* Header/footer skip */
            if (
                section.closest(
                    "header, nav, footer"
                )
            ) {
                return;
            }


            section.classList.add(
                "tg-reveal"
            );


            gsap.fromTo(

                section,

                {
                    opacity: 0,
                    y: 45
                },

                {
                    opacity: 1,
                    y: 0,

                    duration: 0.8,

                    ease: "power3.out",

                    scrollTrigger: {

                        trigger: section,

                        start: "top 88%",

                        end: "top 60%",

                        scrub: 0.7

                    }

                }

            );

        });


    /* =====================================================
       CARD ANIMATION
       ===================================================== */

    const cardGroups = [

        ".destination-card",

        ".package-card",

        ".highlight-card",

        ".grid > article",

        ".grid > .group",

        ".grid > .bg-white"

    ];


    cardGroups.forEach((selector) => {


        gsap.utils
            .toArray(selector)
            .forEach((card) => {


                if (
                    card.closest(
                        "header, nav, footer"
                    )
                ) {
                    return;
                }


                gsap.fromTo(

                    card,

                    {
                        opacity: 0,
                        y: 35
                    },

                    {
                        opacity: 1,

                        y: 0,

                        duration: 0.65,

                        ease: "power3.out",

                        scrollTrigger: {

                            trigger: card,

                            start: "top 92%",

                            toggleActions:
                                "play none none reverse"

                        }

                    }

                );

            });

    });


    /* =====================================================
       IMAGE PARALLAX

       IMPORTANT:
       Image scale 1 hi rahega.
       Isse website zoom jaisi feel nahi hogi.
       Sirf halka movement hoga.
       ===================================================== */

    document
        .querySelectorAll(
            "main section img, body > section img"
        )
        .forEach((img) => {


            if (
                img.closest(
                    "header, nav, footer"
                )
            ) {
                return;
            }


            img.classList.add(
                "tg-parallax-img"
            );


            gsap.to(img, {

                yPercent: -4,

                scale: 1,

                ease: "none",

                scrollTrigger: {

                    trigger: img,

                    start: "top bottom",

                    end: "bottom top",

                    scrub: 1

                }

            });

        });


    /* =====================================================
       HEADINGS REVEAL
       ===================================================== */

    document
        .querySelectorAll(
            "section h1, section h2, section h3"
        )
        .forEach((heading) => {


            if (
                heading.closest(
                    "header, nav, footer"
                )
            ) {
                return;
            }


            gsap.fromTo(

                heading,

                {
                    opacity: 0,
                    y: 22
                },

                {

                    opacity: 1,

                    y: 0,

                    duration: 0.6,

                    ease: "power2.out",

                    scrollTrigger: {

                        trigger: heading,

                        start: "top 90%",

                        toggleActions:
                            "play none none reverse"

                    }

                }

            );

        });


    /* =====================================================
       FINAL SCROLLTRIGGER REFRESH
       ===================================================== */

    ScrollTrigger.refresh();

});