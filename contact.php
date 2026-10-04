<?php

session_start();

require_once "config/database.php";
require_once "includes/user-auth.php";

$success = "";
$error = "";


/* =========================
   FORM SUBMISSION
========================= */

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $name = trim($_POST["name"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $phone = trim($_POST["phone"] ?? "");
    $subject = trim($_POST["subject"] ?? "");
    $message = trim($_POST["message"] ?? "");


    /* Validation */

    if ($name === "") {

        $error = "Please enter your name.";

    } elseif ($email === "") {

        $error = "Please enter your email address.";

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $error = "Please enter a valid email address.";

    } elseif ($message === "") {

        $error = "Please enter your message.";

    } else {

        try {

            $stmt = $pdo->prepare("
                INSERT INTO contacts
                (name, email, phone, subject, message)
                VALUES (?, ?, ?, ?, ?)
            ");

            $stmt->execute([
                $name,
                $email,
                $phone,
                $subject,
                $message
            ]);

            $success = "Thank you! Your message has been sent successfully.";

            /* Clear form values */

            $name = "";
            $email = "";
            $phone = "";
            $subject = "";
            $message = "";

        } catch (PDOException $e) {

            $error = "Something went wrong. Please try again later.";

        }

    }
}


require_once "includes/header.php";
require_once "includes/navbar.php";

?>


<!-- =========================
     HERO SECTION
========================= -->

<section class="relative bg-slate-900 text-white overflow-hidden">

    <div class="absolute inset-0 bg-gradient-to-r from-slate-950 via-slate-900 to-sky-900"></div>

    <div class="relative max-w-7xl mx-auto px-6 py-20 md:py-24">

        <div class="max-w-3xl">

            <span class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-sky-500/20 text-sky-300 text-sm font-semibold mb-5">

                <i class="fa-solid fa-headset"></i>

                TravelGo Support

            </span>


            <h1 class="text-4xl md:text-5xl lg:text-6xl font-extrabold leading-tight mb-6">

                Let's Plan Your
                <span class="text-sky-400">
                    Next Journey
                </span>

            </h1>


            <p class="text-slate-300 text-lg leading-8 max-w-2xl">

                Have a question about our destinations, tour packages or bookings?
                Get in touch with our TravelGo team and we'll be happy to help.

            </p>

        </div>

    </div>

</section>


<!-- =========================
     CONTACT SECTION
========================= -->

<section class="py-16 md:py-20 bg-slate-50">

    <div class="max-w-7xl mx-auto px-6">

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">


            <!-- =========================
                 CONTACT INFORMATION
            ========================= -->

            <div class="lg:col-span-1">

                <div class="bg-slate-900 text-white rounded-3xl p-7 md:p-8 h-full">

                    <span class="text-sky-400 font-bold uppercase tracking-wider text-sm">
                        Get In Touch
                    </span>


                    <h2 class="text-3xl font-extrabold mt-3 mb-5">
                        Contact TravelGo
                    </h2>


                    <p class="text-slate-300 leading-7 mb-8">

                        Our team is here to help you with your travel plans,
                        package details, bookings and other questions.

                    </p>


                    <!-- Phone -->

                    <div class="flex gap-4 mb-6">

                        <div class="w-12 h-12 shrink-0 rounded-xl bg-sky-500/20 text-sky-400 flex items-center justify-center text-lg">

                            <i class="fa-solid fa-phone"></i>

                        </div>


                        <div>

                            <p class="text-sm text-slate-400 mb-1">
                                Phone
                            </p>

                            <a
                                href="tel:+919999999999"
                                class="font-semibold hover:text-sky-400 transition"
                            >
                                +91 99999 99999
                            </a>

                        </div>

                    </div>


                    <!-- Email -->

                    <div class="flex gap-4 mb-6">

                        <div class="w-12 h-12 shrink-0 rounded-xl bg-sky-500/20 text-sky-400 flex items-center justify-center text-lg">

                            <i class="fa-solid fa-envelope"></i>

                        </div>


                        <div>

                            <p class="text-sm text-slate-400 mb-1">
                                Email
                            </p>

                            <a
                                href="mailto:info@travelgo.com"
                                class="font-semibold hover:text-sky-400 transition break-all"
                            >
                                info@travelgo.com
                            </a>

                        </div>

                    </div>


                    <!-- Location -->

                    <div class="flex gap-4 mb-6">

                        <div class="w-12 h-12 shrink-0 rounded-xl bg-sky-500/20 text-sky-400 flex items-center justify-center text-lg">

                            <i class="fa-solid fa-location-dot"></i>

                        </div>


                        <div>

                            <p class="text-sm text-slate-400 mb-1">
                                Office
                            </p>

                            <p class="font-semibold">
                                Rajasthan, India
                            </p>

                        </div>

                    </div>


                    <!-- Working Hours -->

                    <div class="flex gap-4">

                        <div class="w-12 h-12 shrink-0 rounded-xl bg-sky-500/20 text-sky-400 flex items-center justify-center text-lg">

                            <i class="fa-solid fa-clock"></i>

                        </div>


                        <div>

                            <p class="text-sm text-slate-400 mb-1">
                                Working Hours
                            </p>

                            <p class="font-semibold">
                                Mon - Sat: 9:00 AM - 7:00 PM
                            </p>

                        </div>

                    </div>


                    <!-- Social -->

                    <div class="border-t border-slate-700 mt-8 pt-7">

                        <p class="text-sm text-slate-400 mb-4">
                            Follow us
                        </p>


                        <div class="flex gap-3">

                            <a
                                href="#"
                                class="w-10 h-10 rounded-full bg-white/10 hover:bg-sky-500 flex items-center justify-center transition"
                            >
                                <i class="fa-brands fa-facebook-f"></i>
                            </a>


                            <a
                                href="#"
                                class="w-10 h-10 rounded-full bg-white/10 hover:bg-sky-500 flex items-center justify-center transition"
                            >
                                <i class="fa-brands fa-instagram"></i>
                            </a>


                            <a
                                href="#"
                                class="w-10 h-10 rounded-full bg-white/10 hover:bg-sky-500 flex items-center justify-center transition"
                            >
                                <i class="fa-brands fa-youtube"></i>
                            </a>


                            <a
                                href="#"
                                class="w-10 h-10 rounded-full bg-white/10 hover:bg-sky-500 flex items-center justify-center transition"
                            >
                                <i class="fa-brands fa-linkedin-in"></i>
                            </a>

                        </div>

                    </div>

                </div>

            </div>


            <!-- =========================
                 CONTACT FORM
            ========================= -->

            <div class="lg:col-span-2">

                <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-7 md:p-10">

                    <div class="mb-8">

                        <span class="text-sky-500 font-bold uppercase tracking-wider text-sm">
                            Send Us A Message
                        </span>

                        <h2 class="text-3xl font-extrabold text-slate-900 mt-3">
                            How Can We Help?
                        </h2>

                        <p class="text-slate-600 mt-3">
                            Fill out the form below and our team will get back to you.
                        </p>

                    </div>


                    <!-- =========================
                         SUCCESS MESSAGE
                    ========================= -->

                    <?php if ($success): ?>

                        <div class="mb-6 flex items-start gap-3 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-700 px-5 py-4">

                            <i class="fa-solid fa-circle-check mt-1"></i>

                            <p class="font-medium">
                                <?php echo htmlspecialchars($success); ?>
                            </p>

                        </div>

                    <?php endif; ?>


                    <!-- =========================
                         ERROR MESSAGE
                    ========================= -->

                    <?php if ($error): ?>

                        <div class="mb-6 flex items-start gap-3 rounded-xl bg-red-50 border border-red-200 text-red-700 px-5 py-4">

                            <i class="fa-solid fa-circle-exclamation mt-1"></i>

                            <p class="font-medium">
                                <?php echo htmlspecialchars($error); ?>
                            </p>

                        </div>

                    <?php endif; ?>


                    <!-- FORM -->

                    <form
                        method="POST"
                        action=""
                        class="space-y-6"
                    >


                        <!-- Name + Email -->

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">


                            <!-- Name -->

                            <div>

                                <label
                                    for="name"
                                    class="block text-sm font-semibold text-slate-700 mb-2"
                                >
                                    Full Name
                                    <span class="text-red-500">*</span>
                                </label>


                                <div class="relative">

                                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400">

                                        <i class="fa-solid fa-user"></i>

                                    </span>


                                    <input
                                        type="text"
                                        id="name"
                                        name="name"
                                        value="<?php echo htmlspecialchars($name ?? ''); ?>"
                                        placeholder="Enter your name"
                                        required
                                        class="w-full pl-11 pr-4 py-3.5 rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-sky-500 focus:ring-2 focus:ring-sky-100 outline-none transition"
                                    >

                                </div>

                            </div>


                            <!-- Email -->

                            <div>

                                <label
                                    for="email"
                                    class="block text-sm font-semibold text-slate-700 mb-2"
                                >
                                    Email Address
                                    <span class="text-red-500">*</span>
                                </label>


                                <div class="relative">

                                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400">

                                        <i class="fa-solid fa-envelope"></i>

                                    </span>


                                    <input
                                        type="email"
                                        id="email"
                                        name="email"
                                        value="<?php echo htmlspecialchars($email ?? ''); ?>"
                                        placeholder="Enter your email"
                                        required
                                        class="w-full pl-11 pr-4 py-3.5 rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-sky-500 focus:ring-2 focus:ring-sky-100 outline-none transition"
                                    >

                                </div>

                            </div>

                        </div>


                        <!-- Phone + Subject -->

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">


                            <!-- Phone -->

                            <div>

                                <label
                                    for="phone"
                                    class="block text-sm font-semibold text-slate-700 mb-2"
                                >
                                    Phone Number
                                </label>


                                <div class="relative">

                                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400">

                                        <i class="fa-solid fa-phone"></i>

                                    </span>


                                    <input
                                        type="tel"
                                        id="phone"
                                        name="phone"
                                        value="<?php echo htmlspecialchars($phone ?? ''); ?>"
                                        placeholder="Enter your phone number"
                                        class="w-full pl-11 pr-4 py-3.5 rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-sky-500 focus:ring-2 focus:ring-sky-100 outline-none transition"
                                    >

                                </div>

                            </div>


                            <!-- Subject -->

                            <div>

                                <label
                                    for="subject"
                                    class="block text-sm font-semibold text-slate-700 mb-2"
                                >
                                    Subject
                                </label>


                                <div class="relative">

                                    <span class="absolute left-4 top-1/2 -translate-y-1/2 text-slate-400">

                                        <i class="fa-solid fa-tag"></i>

                                    </span>


                                    <input
                                        type="text"
                                        id="subject"
                                        name="subject"
                                        value="<?php echo htmlspecialchars($subject ?? ''); ?>"
                                        placeholder="What is this about?"
                                        class="w-full pl-11 pr-4 py-3.5 rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-sky-500 focus:ring-2 focus:ring-sky-100 outline-none transition"
                                    >

                                </div>

                            </div>

                        </div>


                        <!-- Message -->

                        <div>

                            <label
                                for="message"
                                class="block text-sm font-semibold text-slate-700 mb-2"
                            >
                                Your Message
                                <span class="text-red-500">*</span>
                            </label>


                            <div class="relative">

                                <span class="absolute left-4 top-4 text-slate-400">

                                    <i class="fa-solid fa-message"></i>

                                </span>


                                <textarea
                                    id="message"
                                    name="message"
                                    rows="6"
                                    placeholder="Tell us how we can help you..."
                                    required
                                    class="w-full pl-11 pr-4 py-3.5 rounded-xl border border-slate-200 bg-slate-50 focus:bg-white focus:border-sky-500 focus:ring-2 focus:ring-sky-100 outline-none transition resize-none"
                                ><?php echo htmlspecialchars($message ?? ''); ?></textarea>

                            </div>

                        </div>


                        <!-- Submit -->

                        <button
                            type="submit"
                            class="w-full md:w-auto inline-flex items-center justify-center gap-2 px-8 py-4 rounded-xl bg-sky-500 hover:bg-sky-600 text-white font-bold shadow-lg shadow-sky-500/20 transition"
                        >

                            <i class="fa-solid fa-paper-plane"></i>

                            Send Message

                        </button>


                    </form>

                </div>

            </div>

        </div>

    </div>

</section>


<!-- =========================
     WHY CONTACT US
========================= -->

<section class="py-16 md:py-20 bg-white">

    <div class="max-w-7xl mx-auto px-6">

        <div class="text-center max-w-2xl mx-auto mb-12">

            <span class="text-sky-500 font-bold uppercase tracking-wider text-sm">
                Why Contact Us?
            </span>

            <h2 class="text-3xl md:text-4xl font-extrabold text-slate-900 mt-3">
                We're Here For You
            </h2>

            <p class="text-slate-600 mt-4 leading-7">
                From choosing the right destination to completing your booking,
                our team is ready to assist you.
            </p>

        </div>


        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">


            <!-- Card 1 -->

            <div class="text-center p-7 rounded-2xl bg-slate-50 border border-slate-100">

                <div class="w-14 h-14 mx-auto rounded-2xl bg-sky-100 text-sky-500 flex items-center justify-center text-xl mb-5">

                    <i class="fa-solid fa-bolt"></i>

                </div>

                <h3 class="text-xl font-bold text-slate-900 mb-3">
                    Quick Response
                </h3>

                <p class="text-slate-600 leading-7">
                    We aim to respond to your queries as quickly as possible.
                </p>

            </div>


  

            <div class="text-center p-7 rounded-2xl bg-slate-50 border border-slate-100">

                <div class="w-14 h-14 mx-auto rounded-2xl bg-emerald-100 text-emerald-500 flex items-center justify-center text-xl mb-5">

                    <i class="fa-solid fa-user-group"></i>

                </div>

                <h3 class="text-xl font-bold text-slate-900 mb-3">
                    Travel Experts
                </h3>

                <p class="text-slate-600 leading-7">
                    Get helpful guidance from our travel planning team.
                </p>

            </div>


            <!-- Card 3 -->

            <div class="text-center p-7 rounded-2xl bg-slate-50 border border-slate-100">

                <div class="w-14 h-14 mx-auto rounded-2xl bg-orange-100 text-orange-500 flex items-center justify-center text-xl mb-5">

                    <i class="fa-solid fa-heart"></i>

                </div>

                <h3 class="text-xl font-bold text-slate-900 mb-3">
                    Customer First
                </h3>

                <p class="text-slate-600 leading-7">
                    Your satisfaction and travel experience are our priority.
                </p>

            </div>


        </div>

    </div>

</section>


<!-- =========================
     CTA
========================= -->

<section class="py-16 bg-slate-900">

    <div class="max-w-5xl mx-auto px-6 text-center text-white">

        <div class="w-16 h-16 mx-auto rounded-2xl bg-sky-500/20 text-sky-400 flex items-center justify-center text-2xl mb-6">

            <i class="fa-solid fa-map-location-dot"></i>

        </div>


        <h2 class="text-3xl md:text-4xl font-extrabold mb-4">

            Ready to Explore?

        </h2>


        <p class="text-slate-300 max-w-2xl mx-auto mb-8">

            Browse our destinations and tour packages and start planning
            your next unforgettable trip.

        </p>


        <div class="flex flex-col sm:flex-row justify-center gap-4">

            <a
                href="destinations.php"
                class="inline-flex items-center justify-center gap-2 px-7 py-3.5 rounded-xl bg-sky-500 hover:bg-sky-600 text-white font-bold transition"
            >

                <i class="fa-solid fa-location-dot"></i>

                Explore Destinations

            </a>


            <a
                href="packages.php"
                class="inline-flex items-center justify-center gap-2 px-7 py-3.5 rounded-xl border border-slate-600 hover:bg-slate-800 text-white font-bold transition"
            >

                <i class="fa-solid fa-suitcase-rolling"></i>

                View Packages

            </a>

        </div>

    </div>

</section>


<?php require_once "includes/footer.php"; ?>