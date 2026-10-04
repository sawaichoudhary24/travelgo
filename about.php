<?php

require_once "config/database.php";
require_once "includes/user-auth.php";

require_once "includes/header.php";
require_once "includes/navbar.php";

?>

<!-- =========================
     ABOUT HERO
========================= -->

<section class="relative  bg-[url('assets/images/about.png')]   bg-cover bg-center">

   
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24 lg:py-32">

        <div class="max-w-3xl">

            <span class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-sky-500/20 text-sky-300 text-sm font-semibold mb-5">

                <i class="fa-solid fa-compass"></i>

                About TravelGo

            </span>

            <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold text-white leading-tight">

                Your Journey,

                <span class="text-sky-400">
                    Our Passion
                </span>

            </h1>

            <p class="mt-6 text-lg text-slate-300 leading-8 max-w-2xl">

                TravelGo helps travelers discover beautiful destinations,
                exciting tour packages and memorable experiences — all in
                one place.

            </p>

        </div>

    </div>

</section>


<!-- =========================
     INTRO
========================= -->

<section class="py-16 lg:py-24 bg-white">

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <div class="grid lg:grid-cols-2 gap-12 lg:gap-20 items-center">


            <!-- LEFT -->

            <div>

                <span class="text-sky-500 font-bold uppercase tracking-wider text-sm">
                    Who We Are
                </span>

                <h2 class="text-3xl sm:text-4xl font-extrabold text-slate-900 mt-3 leading-tight">

                    Making travel simple, exciting and memorable

                </h2>

                <p class="text-slate-600 mt-6 leading-8">

                    TravelGo is a tours and travel platform designed to make
                    trip planning easier for everyone. From discovering a
                    destination to choosing a package and making a booking,
                    we bring everything together in one convenient platform.

                </p>

                <p class="text-slate-600 mt-4 leading-8">

                    Our goal is to help travelers spend less time planning
                    and more time enjoying their journey. We focus on
                    comfortable travel, carefully planned itineraries and
                    memorable experiences.

                </p>


                <!-- FEATURES -->

                <div class="grid sm:grid-cols-2 gap-5 mt-8">


                    <div class="flex gap-4">

                        <div class="w-12 h-12 rounded-xl bg-sky-100 text-sky-600 flex items-center justify-center flex-shrink-0">

                            <i class="fa-solid fa-map-location-dot text-lg"></i>

                        </div>

                        <div>

                            <h3 class="font-bold text-slate-900">
                                Amazing Destinations
                            </h3>

                            <p class="text-sm text-slate-500 mt-1">
                                Explore beautiful places.
                            </p>

                        </div>

                    </div>


                    <div class="flex gap-4">

                        <div class="w-12 h-12 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center flex-shrink-0">

                            <i class="fa-solid fa-hand-holding-dollar text-lg"></i>

                        </div>

                        <div>

                            <h3 class="font-bold text-slate-900">
                                Great Value
                            </h3>

                            <p class="text-sm text-slate-500 mt-1">
                                Packages for every budget.
                            </p>

                        </div>

                    </div>


                </div>

            </div>


            <!-- RIGHT -->

            <div class="relative">

                <div class="rounded-3xl bg-gradient-to-br from-sky-100 to-blue-50 p-8 sm:p-12">

                    <div class="aspect-square max-w-md mx-auto rounded-3xl bg-gradient-to-br from-sky-500 to-blue-700 flex items-center justify-center shadow-2xl">

                        <div class="text-center text-white">

                            <i class="fa-solid fa-plane-departure text-7xl mb-6"></i>

                            <h3 class="text-3xl font-extrabold">
                                TravelGo
                            </h3>

                            <p class="text-sky-100 mt-2">
                                Explore • Travel • Experience
                            </p>

                        </div>

                    </div>

                </div>


                <!-- FLOATING CARD -->

                <div class="absolute -bottom-6 left-4 sm:left-8 bg-white rounded-2xl shadow-xl border border-slate-100 p-5">

                    <div class="flex items-center gap-3">

                        <div class="w-12 h-12 rounded-xl bg-yellow-100 text-yellow-600 flex items-center justify-center">

                            <i class="fa-solid fa-star"></i>

                        </div>

                        <div>

                            <p class="font-extrabold text-slate-900">
                                Memorable
                            </p>

                            <p class="text-sm text-slate-500">
                                Travel Experiences
                            </p>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

</section>


<!-- =========================
     STATS
========================= -->

<section class="py-16 bg-slate-50">

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <div class="text-center mb-12">

            <span class="text-sky-500 font-bold uppercase tracking-wider text-sm">
                TravelGo At A Glance
            </span>

            <h2 class="text-3xl sm:text-4xl font-extrabold text-slate-900 mt-3">
                Built for travelers
            </h2>

        </div>


        <div class="grid grid-cols-2 lg:grid-cols-4 gap-5">


            <div class="bg-white rounded-2xl p-6 text-center border border-slate-200">

                <div class="w-14 h-14 mx-auto rounded-xl bg-sky-100 text-sky-600 flex items-center justify-center">

                    <i class="fa-solid fa-location-dot text-xl"></i>

                </div>

                <h3 class="text-3xl font-extrabold text-slate-900 mt-4">
                    50+
                </h3>

                <p class="text-slate-500 mt-1">
                    Destinations
                </p>

            </div>


            <div class="bg-white rounded-2xl p-6 text-center border border-slate-200">

                <div class="w-14 h-14 mx-auto rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center">

                    <i class="fa-solid fa-suitcase-rolling text-xl"></i>

                </div>

                <h3 class="text-3xl font-extrabold text-slate-900 mt-4">
                    100+
                </h3>

                <p class="text-slate-500 mt-1">
                    Tour Packages
                </p>

            </div>


            <div class="bg-white rounded-2xl p-6 text-center border border-slate-200">

                <div class="w-14 h-14 mx-auto rounded-xl bg-violet-100 text-violet-600 flex items-center justify-center">

                    <i class="fa-solid fa-users text-xl"></i>

                </div>

                <h3 class="text-3xl font-extrabold text-slate-900 mt-4">
                    1K+
                </h3>

                <p class="text-slate-500 mt-1">
                    Happy Travelers
                </p>

            </div>


            <div class="bg-white rounded-2xl p-6 text-center border border-slate-200">

                <div class="w-14 h-14 mx-auto rounded-xl bg-orange-100 text-orange-600 flex items-center justify-center">

                    <i class="fa-solid fa-headset text-xl"></i>

                </div>

                <h3 class="text-3xl font-extrabold text-slate-900 mt-4">
                    24/7
                </h3>

                <p class="text-slate-500 mt-1">
                    Travel Support
                </p>

            </div>


        </div>

    </div>

</section>


<!-- =========================
     WHY TRAVELGO
========================= -->

<section class="py-16 lg:py-24 bg-white">

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">


        <div class="text-center max-w-3xl mx-auto mb-14">

            <span class="text-sky-500 font-bold uppercase tracking-wider text-sm">
                Why TravelGo
            </span>

            <h2 class="text-3xl sm:text-4xl font-extrabold text-slate-900 mt-3">

                Everything you need for a better trip

            </h2>

            <p class="text-slate-500 mt-4 leading-7">

                We combine easy booking, carefully planned packages and
                traveler-focused support to make every trip easier.

            </p>

        </div>


        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">


            <!-- CARD 1 -->

            <div class="p-7 rounded-2xl border border-slate-200 hover:shadow-xl hover:-translate-y-1 transition duration-300">

                <div class="w-14 h-14 rounded-xl bg-sky-100 text-sky-600 flex items-center justify-center">

                    <i class="fa-solid fa-route text-xl"></i>

                </div>

                <h3 class="text-xl font-bold text-slate-900 mt-5">
                    Carefully Planned Trips
                </h3>

                <p class="text-slate-500 mt-3 leading-7">

                    Our packages are designed with practical itineraries
                    so you can enjoy your destination without unnecessary
                    planning stress.

                </p>

            </div>


            <!-- CARD 2 -->

            <div class="p-7 rounded-2xl border border-slate-200 hover:shadow-xl hover:-translate-y-1 transition duration-300">

                <div class="w-14 h-14 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center">

                    <i class="fa-solid fa-wallet text-xl"></i>

                </div>

                <h3 class="text-xl font-bold text-slate-900 mt-5">
                    Transparent Pricing
                </h3>

                <p class="text-slate-500 mt-3 leading-7">

                    See package prices clearly and choose a trip that
                    matches your budget and travel plans.

                </p>

            </div>


            <!-- CARD 3 -->

            <div class="p-7 rounded-2xl border border-slate-200 hover:shadow-xl hover:-translate-y-1 transition duration-300">

                <div class="w-14 h-14 rounded-xl bg-violet-100 text-violet-600 flex items-center justify-center">

                    <i class="fa-solid fa-calendar-check text-xl"></i>

                </div>

                <h3 class="text-xl font-bold text-slate-900 mt-5">
                    Easy Booking
                </h3>

                <p class="text-slate-500 mt-3 leading-7">

                    Choose your package, select your travel date and
                    complete your booking in just a few simple steps.

                </p>

            </div>


            <!-- CARD 4 -->

            <div class="p-7 rounded-2xl border border-slate-200 hover:shadow-xl hover:-translate-y-1 transition duration-300">

                <div class="w-14 h-14 rounded-xl bg-orange-100 text-orange-600 flex items-center justify-center">

                    <i class="fa-solid fa-headset text-xl"></i>

                </div>

                <h3 class="text-xl font-bold text-slate-900 mt-5">
                    Traveler Support
                </h3>

                <p class="text-slate-500 mt-3 leading-7">

                    Our support-focused approach helps travelers get
                    assistance whenever they need it.

                </p>

            </div>


            <!-- CARD 5 -->

            <div class="p-7 rounded-2xl border border-slate-200 hover:shadow-xl hover:-translate-y-1 transition duration-300">

                <div class="w-14 h-14 rounded-xl bg-pink-100 text-pink-600 flex items-center justify-center">

                    <i class="fa-solid fa-heart text-xl"></i>

                </div>

                <h3 class="text-xl font-bold text-slate-900 mt-5">
                    Memorable Experiences
                </h3>

                <p class="text-slate-500 mt-3 leading-7">

                    We want every journey to become a story worth
                    remembering and sharing.

                </p>

            </div>


            <!-- CARD 6 -->

            <div class="p-7 rounded-2xl border border-slate-200 hover:shadow-xl hover:-translate-y-1 transition duration-300">

                <div class="w-14 h-14 rounded-xl bg-yellow-100 text-yellow-600 flex items-center justify-center">

                    <i class="fa-solid fa-shield-heart text-xl"></i>

                </div>

                <h3 class="text-xl font-bold text-slate-900 mt-5">
                    Travel With Confidence
                </h3>

                <p class="text-slate-500 mt-3 leading-7">

                    Clear information and a straightforward booking
                    process help you travel with confidence.

                </p>

            </div>


        </div>

    </div>

</section>


<!-- =========================
     OUR MISSION
========================= -->

<section class="py-16 lg:py-24 bg-slate-900">

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <div class="grid lg:grid-cols-2 gap-12 items-center">


            <div>

                <span class="text-sky-400 font-bold uppercase tracking-wider text-sm">
                    Our Mission
                </span>

                <h2 class="text-3xl sm:text-4xl font-extrabold text-white mt-3">

                    Helping people explore more of the world

                </h2>

                <p class="text-slate-300 leading-8 mt-6">

                    We believe travel should be exciting, accessible and
                    simple. TravelGo is built around that idea — giving
                    travelers the tools and information they need to
                    discover new places and create meaningful memories.

                </p>


                <div class="mt-8">

                    <a
                        href="packages.php"
                        class="inline-flex items-center gap-2 px-6 py-3.5 rounded-xl bg-sky-500 hover:bg-sky-600 text-white font-bold transition">

                        Explore Packages

                        <i class="fa-solid fa-arrow-right"></i>

                    </a>

                </div>

            </div>


            <div class="grid grid-cols-2 gap-4">


                <div class="bg-white/10 border border-white/10 rounded-2xl p-6">

                    <i class="fa-solid fa-earth-americas text-3xl text-sky-400"></i>

                    <h3 class="text-white font-bold text-lg mt-4">
                        Discover
                    </h3>

                    <p class="text-slate-400 text-sm mt-2">
                        Find destinations worth exploring.
                    </p>

                </div>


                <div class="bg-white/10 border border-white/10 rounded-2xl p-6">

                    <i class="fa-solid fa-person-walking-luggage text-3xl text-emerald-400"></i>

                    <h3 class="text-white font-bold text-lg mt-4">
                        Explore
                    </h3>

                    <p class="text-slate-400 text-sm mt-2">
                        Experience new places and cultures.
                    </p>

                </div>


                <div class="bg-white/10 border border-white/10 rounded-2xl p-6">

                    <i class="fa-solid fa-camera-retro text-3xl text-violet-400"></i>

                    <h3 class="text-white font-bold text-lg mt-4">
                        Experience
                    </h3>

                    <p class="text-slate-400 text-sm mt-2">
                        Create memories along the way.
                    </p>

                </div>


                <div class="bg-white/10 border border-white/10 rounded-2xl p-6">

                    <i class="fa-solid fa-share-nodes text-3xl text-orange-400"></i>

                    <h3 class="text-white font-bold text-lg mt-4">
                        Remember
                    </h3>

                    <p class="text-slate-400 text-sm mt-2">
                        Take stories home with you.
                    </p>

                </div>


            </div>

        </div>

    </div>

</section>


<!-- =========================
     CTA
========================= -->

<section class="py-16 bg-sky-500">

    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 text-center">

        <div class="w-16 h-16 mx-auto rounded-2xl bg-white/20 text-white flex items-center justify-center">

            <i class="fa-solid fa-plane text-2xl"></i>

        </div>

        <h2 class="text-3xl sm:text-4xl font-extrabold text-white mt-6">

            Ready to start your next journey?

        </h2>

        <p class="text-sky-100 mt-4 max-w-2xl mx-auto leading-7">

            Explore our destinations and tour packages and find the
            perfect trip for your next adventure.

        </p>

        <div class="flex flex-col sm:flex-row justify-center gap-3 mt-8">

            <a
                href="destinations.php"
                class="inline-flex items-center justify-center gap-2 px-6 py-3.5 rounded-xl bg-white text-sky-600 hover:bg-slate-100 font-bold transition">

                <i class="fa-solid fa-location-dot"></i>

                Explore Destinations

            </a>


            <a
                href="contact.php"
                class="inline-flex items-center justify-center gap-2 px-6 py-3.5 rounded-xl border border-white/50 text-white hover:bg-white/10 font-bold transition">

                <i class="fa-solid fa-envelope"></i>

                Contact Us

            </a>

        </div>

    </div>

</section>


<?php

require_once "includes/footer.php";

?>