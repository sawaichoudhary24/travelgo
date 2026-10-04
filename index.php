<?php
require_once "config/database.php";
require_once "includes/header.php";
require_once "includes/navbar.php";
?>

<!-- ================= HERO SECTION ================= -->
<section class="relative min-h-[650px] flex items-center overflow-hidden">

    <!-- Background -->
    <div class="absolute inset-0">
        <img
            src="https://images.unsplash.com/photo-1507525428034-b723cf961d3e?auto=format&fit=crop&w=2000&q=85"
            alt="Travel Destination"
            class="w-full h-full object-cover"
        >

        <div class="absolute inset-0 bg-slate-950/55"></div>
    </div>

    <!-- Hero Content -->
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full py-24">

        <div class="max-w-3xl text-white">

            <span class="inline-flex items-center gap-2 bg-white/15 backdrop-blur-md px-4 py-2 rounded-full text-sm font-semibold mb-6">
                <i class="fa-solid fa-earth-americas text-amber-400"></i>
                Explore The World With Us
            </span>

            <h1 class="text-4xl sm:text-5xl lg:text-7xl font-extrabold leading-tight">
                Travel More.
                <span class="text-sky-400">Live More.</span>
            </h1>

            <p class="text-lg sm:text-xl text-slate-200 mt-6 max-w-2xl leading-8">
                Discover beautiful destinations, amazing experiences
                and unforgettable journeys with TravelGo Tours & Travels.
            </p>

            <div class="flex flex-wrap gap-4 mt-8">

                <a
                    href="packages.php"
                    class="bg-sky-500 hover:bg-sky-600 text-white px-7 py-4 rounded-xl font-bold transition"
                >
                    Explore Packages
                    <i class="fa-solid fa-arrow-right ml-2"></i>
                </a>

                <a
                    href="destinations.php"
                    class="border border-white/60 hover:bg-white hover:text-slate-900 text-white px-7 py-4 rounded-xl font-bold transition"
                >
                    View Destinations
                </a>

            </div>

        </div>

    </div>

</section>


<!-- ================= SEARCH BOX ================= -->
<section class="relative -mt-16 z-10">

    <div class="max-w-6xl mx-auto px-4">

        <div class="bg-white rounded-2xl shadow-2xl p-5 sm:p-7">

            <div class="grid md:grid-cols-4 gap-4">

                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">
                        <i class="fa-solid fa-location-dot text-sky-500 mr-1"></i>
                        Destination
                    </label>

                    <select class="w-full border border-slate-200 rounded-xl px-4 py-3.5 outline-none focus:border-sky-500">
                        <option>Select destination</option>
                        <option>Rajasthan</option>
                        <option>Goa</option>
                        <option>Manali</option>
                        <option>Kerala</option>
                        <option>Kashmir</option>
                    </select>
                </div>


                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">
                        <i class="fa-solid fa-calendar text-sky-500 mr-1"></i>
                        Travel Date
                    </label>

                    <input
                        type="date"
                        class="w-full border border-slate-200 rounded-xl px-4 py-3.5 outline-none focus:border-sky-500"
                    >
                </div>


                <div>
                    <label class="block text-sm font-semibold text-slate-700 mb-2">
                        <i class="fa-solid fa-users text-sky-500 mr-1"></i>
                        Travelers
                    </label>

                    <select class="w-full border border-slate-200 rounded-xl px-4 py-3.5 outline-none focus:border-sky-500">
                        <option>1 Traveler</option>
                        <option>2 Travelers</option>
                        <option>3 Travelers</option>
                        <option>4 Travelers</option>
                        <option>5+ Travelers</option>
                    </select>
                </div>


                <div class="flex items-end">

                    <button
                        class="w-full bg-amber-500 hover:bg-amber-600 text-white rounded-xl px-6 py-3.5 font-bold transition"
                    >
                        <i class="fa-solid fa-magnifying-glass mr-2"></i>
                        Search Trips
                    </button>

                </div>

            </div>

        </div>

    </div>

</section>


<!-- ================= POPULAR DESTINATIONS ================= -->
<section class="py-24 bg-slate-50">

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <div class="text-center max-w-2xl mx-auto">

            <span class="text-sky-500 font-bold uppercase tracking-widest text-sm">
                Explore Places
            </span>

            <h2 class="text-3xl sm:text-4xl font-extrabold text-slate-900 mt-3">
                Popular Destinations
            </h2>

            <p class="text-slate-500 mt-4">
                Discover some of India's most beautiful and exciting
                travel destinations.
            </p>

        </div>


        <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-6 mt-12">


            <!-- Destination 1 -->
            <div class="group bg-white rounded-2xl overflow-hidden shadow-sm hover:shadow-xl transition">

                <div class="relative h-64 overflow-hidden">

                    <img
                        src="https://images.unsplash.com/photo-1477587458883-47145ed94245?auto=format&fit=crop&w=800&q=80"
                        alt="Rajasthan"
                        class="w-full h-full object-cover group-hover:scale-110 transition duration-700"
                    >

                    <div class="absolute inset-0 bg-gradient-to-t from-black/70 to-transparent"></div>

                    <div class="absolute bottom-5 left-5 text-white">

                        <h3 class="text-2xl font-bold">
                            Rajasthan
                        </h3>

                        <p class="text-sm text-slate-200">
                            Land of Kings
                        </p>

                    </div>

                </div>

            </div>


            <!-- Destination 2 -->
            <div class="group bg-white rounded-2xl overflow-hidden shadow-sm hover:shadow-xl transition">

                <div class="relative h-64 overflow-hidden">

                    <img
                        src="https://images.unsplash.com/photo-1512343879784-a960bf40e7f2?auto=format&fit=crop&w=800&q=80"
                        alt="Goa"
                        class="w-full h-full object-cover group-hover:scale-110 transition duration-700"
                    >

                    <div class="absolute inset-0 bg-gradient-to-t from-black/70 to-transparent"></div>

                    <div class="absolute bottom-5 left-5 text-white">

                        <h3 class="text-2xl font-bold">
                            Goa
                        </h3>

                        <p class="text-sm text-slate-200">
                            Beaches & Fun
                        </p>

                    </div>

                </div>

            </div>


            <!-- Destination 3 -->
            <div class="group bg-white rounded-2xl overflow-hidden shadow-sm hover:shadow-xl transition">

                <div class="relative h-64 overflow-hidden">

                    <img
                        src="https://images.unsplash.com/photo-1506905925346-21bda4d32df4?auto=format&fit=crop&w=800&q=80"
                        alt="Manali"
                        class="w-full h-full object-cover group-hover:scale-110 transition duration-700"
                    >

                    <div class="absolute inset-0 bg-gradient-to-t from-black/70 to-transparent"></div>

                    <div class="absolute bottom-5 left-5 text-white">

                        <h3 class="text-2xl font-bold">
                            Manali
                        </h3>

                        <p class="text-sm text-slate-200">
                            Mountains & Adventure
                        </p>

                    </div>

                </div>

            </div>


            <!-- Destination 4 -->
            <div class="group bg-white rounded-2xl overflow-hidden shadow-sm hover:shadow-xl transition">

                <div class="relative h-64 overflow-hidden">

                    <img
                        src="https://images.unsplash.com/photo-1602216056096-3b40cc0c9944?auto=format&fit=crop&w=800&q=80"
                        alt="Kerala"
                        class="w-full h-full object-cover group-hover:scale-110 transition duration-700"
                    >

                    <div class="absolute inset-0 bg-gradient-to-t from-black/70 to-transparent"></div>

                    <div class="absolute bottom-5 left-5 text-white">

                        <h3 class="text-2xl font-bold">
                            Kerala
                        </h3>

                        <p class="text-sm text-slate-200">
                            God's Own Country
                        </p>

                    </div>

                </div>

            </div>

        </div>


        <div class="text-center mt-10">

            <a
                href="destinations.php"
                class="inline-flex items-center gap-2 text-sky-500 font-bold hover:text-sky-600"
            >
                View All Destinations
                <i class="fa-solid fa-arrow-right"></i>
            </a>

        </div>

    </div>

</section>


<!-- ================= TOUR PACKAGES ================= -->
<section class="py-24 bg-white">

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-5">

            <div>

                <span class="text-sky-500 font-bold uppercase tracking-widest text-sm">
                    Best Deals
                </span>

                <h2 class="text-3xl sm:text-4xl font-extrabold text-slate-900 mt-3">
                    Featured Tour Packages
                </h2>

                <p class="text-slate-500 mt-4 max-w-xl">
                    Choose from our handpicked tour packages designed
                    for unforgettable travel experiences.
                </p>

            </div>

            <a
                href="packages.php"
                class="text-sky-500 font-bold"
            >
                View All Packages
                <i class="fa-solid fa-arrow-right ml-2"></i>
            </a>

        </div>


        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-7 mt-12">


            <!-- Package 1 -->
            <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden hover:shadow-xl transition">

                <div class="relative h-60">

                    <img
                        src="https://images.unsplash.com/photo-1524492412937-b28074a5d7da?auto=format&fit=crop&w=900&q=80"
                        alt="Golden Triangle"
                        class="w-full h-full object-cover"
                    >

                    <span class="absolute top-4 left-4 bg-amber-500 text-white px-3 py-1.5 rounded-full text-xs font-bold">
                        POPULAR
                    </span>

                </div>

                <div class="p-6">

                    <div class="flex items-center gap-1 text-amber-400 text-sm">
                        <i class="fa-solid fa-star"></i>
                        <i class="fa-solid fa-star"></i>
                        <i class="fa-solid fa-star"></i>
                        <i class="fa-solid fa-star"></i>
                        <i class="fa-solid fa-star"></i>

                        <span class="text-slate-400 ml-1">
                            (48)
                        </span>
                    </div>

                    <h3 class="text-xl font-bold text-slate-900 mt-3">
                        Golden Triangle Tour
                    </h3>

                    <p class="text-slate-500 mt-2 text-sm">
                        Delhi • Agra • Jaipur
                    </p>

                    <div class="flex items-center justify-between mt-6">

                        <div>
                            <span class="text-xs text-slate-400">
                                Starting from
                            </span>

                            <p class="text-2xl font-extrabold text-sky-500">
                                ₹12,999
                            </p>
                        </div>

                        <a
                            href="package-details.php"
                            class="bg-sky-500 hover:bg-sky-600 text-white px-4 py-2.5 rounded-lg font-semibold"
                        >
                            View
                        </a>

                    </div>

                </div>

            </div>


            <!-- Package 2 -->
            <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden hover:shadow-xl transition">

                <div class="relative h-60">

                    <img
                        src="https://images.unsplash.com/photo-1506461883276-594a12b11cf3?auto=format&fit=crop&w=900&q=80"
                        alt="Kerala Tour"
                        class="w-full h-full object-cover"
                    >

                    <span class="absolute top-4 left-4 bg-sky-500 text-white px-3 py-1.5 rounded-full text-xs font-bold">
                        BEST VALUE
                    </span>

                </div>

                <div class="p-6">

                    <div class="flex items-center gap-1 text-amber-400 text-sm">
                        <i class="fa-solid fa-star"></i>
                        <i class="fa-solid fa-star"></i>
                        <i class="fa-solid fa-star"></i>
                        <i class="fa-solid fa-star"></i>
                        <i class="fa-solid fa-star-half-stroke"></i>

                        <span class="text-slate-400 ml-1">
                            (36)
                        </span>
                    </div>

                    <h3 class="text-xl font-bold text-slate-900 mt-3">
                        Magical Kerala
                    </h3>

                    <p class="text-slate-500 mt-2 text-sm">
                        Kochi • Munnar • Alleppey
                    </p>

                    <div class="flex items-center justify-between mt-6">

                        <div>
                            <span class="text-xs text-slate-400">
                                Starting from
                            </span>

                            <p class="text-2xl font-extrabold text-sky-500">
                                ₹15,499
                            </p>
                        </div>

                        <a
                            href="package-details.php"
                            class="bg-sky-500 hover:bg-sky-600 text-white px-4 py-2.5 rounded-lg font-semibold"
                        >
                            View
                        </a>

                    </div>

                </div>

            </div>


            <!-- Package 3 -->
            <div class="bg-white border border-slate-200 rounded-2xl overflow-hidden hover:shadow-xl transition">

                <div class="relative h-60">

                    <img
                        src="https://images.unsplash.com/photo-1500534623283-312aade485b7?auto=format&fit=crop&w=900&q=80"
                        alt="Manali Tour"
                        class="w-full h-full object-cover"
                    >

                    <span class="absolute top-4 left-4 bg-slate-900 text-white px-3 py-1.5 rounded-full text-xs font-bold">
                        ADVENTURE
                    </span>

                </div>

                <div class="p-6">

                    <div class="flex items-center gap-1 text-amber-400 text-sm">
                        <i class="fa-solid fa-star"></i>
                        <i class="fa-solid fa-star"></i>
                        <i class="fa-solid fa-star"></i>
                        <i class="fa-solid fa-star"></i>
                        <i class="fa-solid fa-star"></i>

                        <span class="text-slate-400 ml-1">
                            (52)
                        </span>
                    </div>

                    <h3 class="text-xl font-bold text-slate-900 mt-3">
                        Manali Adventure
                    </h3>

                    <p class="text-slate-500 mt-2 text-sm">
                        Manali • Solang Valley • Rohtang
                    </p>

                    <div class="flex items-center justify-between mt-6">

                        <div>
                            <span class="text-xs text-slate-400">
                                Starting from
                            </span>

                            <p class="text-2xl font-extrabold text-sky-500">
                                ₹10,999
                            </p>
                        </div>

                        <a
                            href="package-details.php"
                            class="bg-sky-500 hover:bg-sky-600 text-white px-4 py-2.5 rounded-lg font-semibold"
                        >
                            View
                        </a>

                    </div>

                </div>

            </div>

        </div>

    </div>

</section>


<!-- ================= WHY CHOOSE US ================= -->
<section class="py-24 bg-sky-50">

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <div class="text-center max-w-2xl mx-auto">

            <span class="text-sky-500 font-bold uppercase tracking-widest text-sm">
                Why TravelGo
            </span>

            <h2 class="text-3xl sm:text-4xl font-extrabold text-slate-900 mt-3">
                Why Choose Us?
            </h2>

            <p class="text-slate-500 mt-4">
                We make your journey simple, comfortable and memorable.
            </p>

        </div>


        <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-6 mt-12">


            <div class="bg-white p-7 rounded-2xl text-center shadow-sm">

                <div class="w-16 h-16 bg-sky-100 text-sky-500 rounded-2xl flex items-center justify-center mx-auto text-2xl">
                    <i class="fa-solid fa-wallet"></i>
                </div>

                <h3 class="font-bold text-xl mt-5">
                    Best Prices
                </h3>

                <p class="text-slate-500 mt-3 text-sm leading-6">
                    Get competitive prices without compromising on quality.
                </p>

            </div>


            <div class="bg-white p-7 rounded-2xl text-center shadow-sm">

                <div class="w-16 h-16 bg-orange-100 text-orange-500 rounded-2xl flex items-center justify-center mx-auto text-2xl">
                    <i class="fa-solid fa-headset"></i>
                </div>

                <h3 class="font-bold text-xl mt-5">
                    24/7 Support
                </h3>

                <p class="text-slate-500 mt-3 text-sm leading-6">
                    Our travel experts are always ready to help you.
                </p>

            </div>


            <div class="bg-white p-7 rounded-2xl text-center shadow-sm">

                <div class="w-16 h-16 bg-green-100 text-green-500 rounded-2xl flex items-center justify-center mx-auto text-2xl">
                    <i class="fa-solid fa-shield-heart"></i>
                </div>

                <h3 class="font-bold text-xl mt-5">
                    Safe & Secure
                </h3>

                <p class="text-slate-500 mt-3 text-sm leading-6">
                    Your safety and comfort are our top priorities.
                </p>

            </div>


            <div class="bg-white p-7 rounded-2xl text-center shadow-sm">

                <div class="w-16 h-16 bg-purple-100 text-purple-500 rounded-2xl flex items-center justify-center mx-auto text-2xl">
                    <i class="fa-solid fa-map-location-dot"></i>
                </div>

                <h3 class="font-bold text-xl mt-5">
                    Local Experts
                </h3>

                <p class="text-slate-500 mt-3 text-sm leading-6">
                    Explore destinations with experienced local experts.
                </p>

            </div>

        </div>

    </div>

</section>


<!-- ================= TESTIMONIALS ================= -->
<section class="py-24 bg-white">

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        <div class="text-center">

            <span class="text-sky-500 font-bold uppercase tracking-widest text-sm">
                Reviews
            </span>

            <h2 class="text-3xl sm:text-4xl font-extrabold text-slate-900 mt-3">
                What Our Travelers Say
            </h2>

        </div>


        <div class="grid md:grid-cols-3 gap-6 mt-12">


            <div class="border border-slate-200 rounded-2xl p-7">

                <div class="text-amber-400">
                    ★★★★★
                </div>

                <p class="text-slate-600 mt-5 leading-7">
                    "Amazing experience! Everything was perfectly
                    planned and the support team was very helpful."
                </p>

                <div class="flex items-center gap-3 mt-6">

                    <div class="w-11 h-11 rounded-full bg-sky-100 flex items-center justify-center">
                        <i class="fa-solid fa-user text-sky-500"></i>
                    </div>

                    <div>
                        <h4 class="font-bold">
                            Rahul Sharma
                        </h4>

                        <p class="text-xs text-slate-400">
                            Delhi
                        </p>
                    </div>

                </div>

            </div>


            <div class="border border-slate-200 rounded-2xl p-7">

                <div class="text-amber-400">
                    ★★★★★
                </div>

                <p class="text-slate-600 mt-5 leading-7">
                    "Our Kerala trip was fantastic. Great hotels,
                    comfortable travel and excellent service."
                </p>

                <div class="flex items-center gap-3 mt-6">

                    <div class="w-11 h-11 rounded-full bg-sky-100 flex items-center justify-center">
                        <i class="fa-solid fa-user text-sky-500"></i>
                    </div>

                    <div>
                        <h4 class="font-bold">
                            Priya Verma
                        </h4>

                        <p class="text-xs text-slate-400">
                            Jaipur
                        </p>
                    </div>

                </div>

            </div>


            <div class="border border-slate-200 rounded-2xl p-7">

                <div class="text-amber-400">
                    ★★★★★
                </div>

                <p class="text-slate-600 mt-5 leading-7">
                    "Best travel company for family trips.
                    Highly recommended for everyone."
                </p>

                <div class="flex items-center gap-3 mt-6">

                    <div class="w-11 h-11 rounded-full bg-sky-100 flex items-center justify-center">
                        <i class="fa-solid fa-user text-sky-500"></i>
                    </div>

                    <div>
                        <h4 class="font-bold">
                            Amit Singh
                        </h4>

                        <p class="text-xs text-slate-400">
                            Mumbai
                        </p>
                    </div>

                </div>

            </div>

        </div>

    </div>

</section>


<!-- ================= CTA ================= -->
<section class="py-20">

    <div class="max-w-7xl mx-auto px-4">

        <div class="relative overflow-hidden rounded-3xl bg-slate-900 px-7 py-16 sm:px-12 text-center">

            <div class="absolute top-0 left-0 w-40 h-40 bg-sky-500/20 rounded-full blur-3xl"></div>

            <div class="absolute bottom-0 right-0 w-60 h-60 bg-amber-500/20 rounded-full blur-3xl"></div>

            <div class="relative">

                <h2 class="text-3xl sm:text-4xl font-extrabold text-white">
                    Ready For Your Next Adventure?
                </h2>

                <p class="text-slate-300 mt-4 max-w-2xl mx-auto">
                    Let us help you plan a memorable journey that
                    you'll remember for years.
                </p>

                <a
                    href="contact.php"
                    class="inline-block mt-8 bg-amber-500 hover:bg-amber-600 text-white px-8 py-4 rounded-xl font-bold transition"
                >
                    Plan My Trip
                    <i class="fa-solid fa-arrow-right ml-2"></i>
                </a>

            </div>

        </div>

    </div>

</section>


<?php
require_once "includes/footer.php";
?>