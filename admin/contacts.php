<?php

require_once "../includes/auth.php";
require_once "../config/database.php";


/* =========================
   DELETE CONTACT
========================= */

if (isset($_GET['delete'])) {

    $deleteId = (int) $_GET['delete'];

    if ($deleteId >
 0) {

        $deleteStmt = $pdo->prepare("
            DELETE FROM contacts
            WHERE id = ?
        ");

        $deleteStmt->execute([$deleteId]);
    }

    header("Location: contacts.php");
    exit;
}


/* =========================
   SEARCH
========================= */

$search = trim($_GET['search'] ?? '');

if ($search !== '') {

    $stmt = $pdo->prepare("
        SELECT *
        FROM contacts
        WHERE
            name LIKE ?
            OR email LIKE ?
            OR phone LIKE ?
            OR subject LIKE ?
            OR message LIKE ?
        ORDER BY id DESC
    ");

    $searchValue = "%" . $search . "%";

    $stmt->execute([
        $searchValue,
        $searchValue,
        $searchValue,
        $searchValue,
        $searchValue
    ]);

} else {

    $stmt = $pdo->query("
        SELECT *
        FROM contacts
        ORDER BY id DESC
    ");
}

$contacts = $stmt->fetchAll(PDO::FETCH_ASSOC);


/* =========================
   TOTAL CONTACTS
========================= */

$totalContacts = $pdo->query("
    SELECT COUNT(*)
    FROM contacts
")->fetchColumn();

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Contact Messages - TravelGo Admin</title>

    <script src="https://cdn.tailwindcss.com"></script>

    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
    >

    <link rel="stylesheet" href="../assets/css/admin.css">
</head>


<body class="bg-slate-100 text-slate-800 tg-admin-layout">

<?php include_once "sidebar.php"; ?>


<!-- =========================
     HEADER
========================= -->

<header class="bg-white border-b border-slate-200">
    <div class="w-full px-4 sm:px-6 lg:px-8">
        <div class="h-16 flex items-center justify-end">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-full bg-sky-100 text-sky-700 flex items-center justify-center">
                    <i class="fa-solid fa-user"></i>
                </div>
                <div class="leading-tight">
                    <p class="font-semibold text-sm text-slate-800">
                        <?php echo htmlspecialchars($_SESSION['user_name'] ?? 'Admin'); ?>
                    </p>
                    <p class="text-xs text-slate-500">Administrator</p>
                </div>
            </div>
        </div>
    </div>
</header>


<!-- =========================
     MAIN
========================= -->

<main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">


    <!-- PAGE HEADER -->

    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-5 mb-8">

        <div>

            <h2 class="text-2xl sm:text-3xl font-bold text-slate-900">

                Contact Messages

            </h2>

            <p class="text-slate-500 mt-1">

                Manage messages received from website visitors.

            </p>

        </div>


        <!-- TOTAL -->

        <div class="bg-white border border-slate-200 shadow-sm rounded-2xl px-5 py-4">

            <div class="flex items-center gap-3">

                <div class="w-11 h-11 rounded-xl bg-pink-100 text-pink-600 flex items-center justify-center">

                    <i class="fa-solid fa-envelope"></i>

                </div>

                <div>

                    <p class="text-xs text-slate-500">
                        Total Messages
                    </p>

                    <p class="text-2xl font-bold text-slate-900">

                        <?php echo (int)$totalContacts; ?>

                    </p>

                </div>

            </div>

        </div>

    </div>



    <!-- =========================
         SEARCH
    ========================= -->

    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-5 mb-6">

        <form
            method="GET"
            action="contacts.php"
            class="flex flex-col sm:flex-row gap-3"
        >

            <div class="relative flex-1">

                <i class="fa-solid fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-slate-400"></i>

                <input
                    type="text"
                    name="search"
                    value="<?php echo htmlspecialchars($search); ?>"
                    placeholder="Search name, email, phone, subject..."
                    class="w-full pl-11 pr-4 py-3 rounded-xl border border-slate-300 focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-sky-500"
                >

            </div>


            <button
                type="submit"
                class="inline-flex items-center justify-center gap-2 px-6 py-3 rounded-xl bg-sky-500 hover:bg-sky-600 text-white font-semibold transition"
            >

                <i class="fa-solid fa-magnifying-glass"></i>

                Search

            </button>


            <?php if ($search !== ''): ?>

                <a
                    href="contacts.php"
                    class="inline-flex items-center justify-center gap-2 px-6 py-3 rounded-xl bg-slate-200 hover:bg-slate-300 text-slate-700 font-semibold transition"
                >

                    <i class="fa-solid fa-xmark"></i>

                    Clear

                </a>

            <?php endif; ?>

        </form>

    </div>



    <!-- =========================
         DESKTOP TABLE
    ========================= -->

    <div class="hidden lg:block bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">

        <div class="overflow-x-auto">

            <table class="w-full text-left">

                <thead class="bg-slate-50 border-b border-slate-200">

                    <tr>

                        <th class="px-6 py-4 text-xs font-bold uppercase text-slate-500">
                            Visitor
                        </th>

                        <th class="px-6 py-4 text-xs font-bold uppercase text-slate-500">
                            Contact
                        </th>

                        <th class="px-6 py-4 text-xs font-bold uppercase text-slate-500">
                            Subject
                        </th>

                        <th class="px-6 py-4 text-xs font-bold uppercase text-slate-500">
                            Message
                        </th>

                        <th class="px-6 py-4 text-xs font-bold uppercase text-slate-500">
                            Date
                        </th>

                        <th class="px-6 py-4 text-xs font-bold uppercase text-slate-500 text-right">
                            Actions
                        </th>

                    </tr>

                </thead>


                <tbody class="divide-y divide-slate-100">

                    <?php if (empty($contacts)): ?>

                        <tr>

                            <td
                                colspan="6"
                                class="px-6 py-14 text-center"
                            >

                                <div class="flex flex-col items-center">

                                    <div class="w-16 h-16 rounded-full bg-slate-100 flex items-center justify-center">

                                        <i class="fa-regular fa-envelope-open text-2xl text-slate-400"></i>

                                    </div>

                                    <h3 class="font-bold text-slate-700 mt-4">
                                        No Messages Found
                                    </h3>

                                    <p class="text-sm text-slate-500 mt-1">
                                        No contact messages match your search.
                                    </p>

                                </div>

                            </td>

                        </tr>

                    <?php else: ?>


                        <?php foreach ($contacts as $contact): ?>

                            <tr class="hover:bg-slate-50 transition">


                                <!-- VISITOR -->

                                <td class="px-6 py-5">

                                    <div class="flex items-center gap-3">

                                        <div class="w-10 h-10 rounded-full bg-sky-100 text-sky-600 flex items-center justify-center flex-shrink-0">

                                            <i class="fa-solid fa-user"></i>

                                        </div>

                                        <div>

                                            <p class="font-bold text-slate-900">

                                                <?php echo htmlspecialchars($contact['name']); ?>

                                            </p>

                                            <p class="text-xs text-slate-500">

                                                ID #<?php echo (int)$contact['id']; ?>

                                            </p>

                                        </div>

                                    </div>

                                </td>



                                <!-- CONTACT -->

                                <td class="px-6 py-5">

                                    <div class="space-y-1">

                                        <?php if (!empty($contact['email'])): ?>

                                            <a
                                                href="mailto:<?php echo htmlspecialchars($contact['email']); ?>"
                                                class="flex items-center gap-2 text-sm text-sky-600 hover:text-sky-700"
                                            >

                                                <i class="fa-solid fa-envelope text-xs"></i>

                                                <?php echo htmlspecialchars($contact['email']); ?>

                                            </a>

                                        <?php endif; ?>


                                        <?php if (!empty($contact['phone'])): ?>

                                            <a
                                                href="tel:<?php echo htmlspecialchars($contact['phone']); ?>"
                                                class="flex items-center gap-2 text-sm text-green-600 hover:text-green-700"
                                            >

                                                <i class="fa-solid fa-phone text-xs"></i>

                                                <?php echo htmlspecialchars($contact['phone']); ?>

                                            </a>

                                        <?php endif; ?>

                                    </div>

                                </td>



                                <!-- SUBJECT -->

                                <td class="px-6 py-5">

                                    <span class="inline-flex px-3 py-1 rounded-full bg-slate-100 text-slate-700 text-xs font-semibold">

                                        <?php echo htmlspecialchars($contact['subject'] ?: 'General Inquiry'); ?>

                                    </span>

                                </td>



                                <!-- MESSAGE -->

                                <td class="px-6 py-5 max-w-xs">

                                    <p class="text-sm text-slate-600 line-clamp-2">

                                        <?php echo htmlspecialchars($contact['message']); ?>

                                    </p>

                                </td>



                                <!-- DATE -->

                                <td class="px-6 py-5">

                                    <p class="text-sm font-medium text-slate-700">

                                        <?php
                                        echo date(
                                            "d M Y",
                                            strtotime($contact['created_at'])
                                        );
                                        ?>

                                    </p>

                                    <p class="text-xs text-slate-400 mt-1">

                                        <?php
                                        echo date(
                                            "h:i A",
                                            strtotime($contact['created_at'])
                                        );
                                        ?>

                                    </p>

                                </td>



                                <!-- ACTIONS -->

                                <td class="px-6 py-5">

                                    <div class="flex justify-end gap-2">

                                        <button
                                            type="button"
                                            onclick="openMessage(
                                                <?php echo (int)$contact['id']; ?>,
                                                <?php echo htmlspecialchars(json_encode($contact['name'])); ?>,
                                                <?php echo htmlspecialchars(json_encode($contact['email'] ?? '')); ?>,
                                                <?php echo htmlspecialchars(json_encode($contact['phone'] ?? '')); ?>,
                                                <?php echo htmlspecialchars(json_encode($contact['subject'] ?? '')); ?>,
                                                <?php echo htmlspecialchars(json_encode($contact['message'] ?? '')); ?>,
                                                <?php echo htmlspecialchars(json_encode($contact['created_at'] ?? '')); ?>
                                            )"
                                            class="w-9 h-9 rounded-lg bg-sky-100 text-sky-600 hover:bg-sky-200 flex items-center justify-center transition"
                                            title="View Details"
                                        >

                                            <i class="fa-solid fa-eye"></i>

                                        </button>


                                        <a
                                            href="contacts.php?delete=<?php echo (int)$contact['id']; ?>"
                                            onclick="return confirm('Are you sure you want to delete this message?')"
                                            class="w-9 h-9 rounded-lg bg-red-100 text-red-600 hover:bg-red-200 flex items-center justify-center transition"
                                            title="Delete"
                                        >

                                            <i class="fa-solid fa-trash"></i>

                                        </a>

                                    </div>

                                </td>

                            </tr>

                        <?php endforeach; ?>


                    <?php endif; ?>

                </tbody>

            </table>

        </div>

    </div>



    <!-- =========================
         MOBILE CARDS
    ========================= -->

    <div class="lg:hidden space-y-4">

        <?php if (empty($contacts)): ?>

            <div class="bg-white rounded-2xl border border-slate-200 p-10 text-center">

                <div class="w-16 h-16 mx-auto rounded-full bg-slate-100 flex items-center justify-center">

                    <i class="fa-regular fa-envelope-open text-2xl text-slate-400"></i>

                </div>

                <h3 class="font-bold text-slate-700 mt-4">
                    No Messages Found
                </h3>

                <p class="text-sm text-slate-500 mt-1">
                    No contact messages available.
                </p>

            </div>

        <?php else: ?>


            <?php foreach ($contacts as $contact): ?>

                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5">


                    <!-- TOP -->

                    <div class="flex items-start justify-between gap-3">

                        <div class="flex items-center gap-3">

                            <div class="w-11 h-11 rounded-full bg-sky-100 text-sky-600 flex items-center justify-center flex-shrink-0">

                                <i class="fa-solid fa-user"></i>

                            </div>

                            <div>

                                <h3 class="font-bold text-slate-900">

                                    <?php echo htmlspecialchars($contact['name']); ?>

                                </h3>

                                <p class="text-xs text-slate-500">

                                    Message #<?php echo (int)$contact['id']; ?>

                                </p>

                            </div>

                        </div>


                        <span class="text-xs text-slate-400 whitespace-nowrap">

                            <?php
                            echo date(
                                "d M Y",
                                strtotime($contact['created_at'])
                            );
                            ?>

                        </span>

                    </div>



                    <!-- CONTACT INFO -->

                    <div class="mt-4 space-y-2">

                        <?php if (!empty($contact['email'])): ?>

                            <a
                                href="mailto:<?php echo htmlspecialchars($contact['email']); ?>"
                                class="flex items-center gap-2 text-sm text-sky-600"
                            >

                                <i class="fa-solid fa-envelope w-4"></i>

                                <?php echo htmlspecialchars($contact['email']); ?>

                            </a>

                        <?php endif; ?>


                        <?php if (!empty($contact['phone'])): ?>

                            <a
                                href="tel:<?php echo htmlspecialchars($contact['phone']); ?>"
                                class="flex items-center gap-2 text-sm text-green-600"
                            >

                                <i class="fa-solid fa-phone w-4"></i>

                                <?php echo htmlspecialchars($contact['phone']); ?>

                            </a>

                        <?php endif; ?>

                    </div>



                    <!-- SUBJECT -->

                    <div class="mt-4">

                        <span class="inline-flex px-3 py-1 rounded-full bg-slate-100 text-slate-700 text-xs font-semibold">

                            <?php echo htmlspecialchars($contact['subject'] ?: 'General Inquiry'); ?>

                        </span>

                    </div>



                    <!-- MESSAGE -->

                    <div class="mt-4 p-4 rounded-xl bg-slate-50">

                        <p class="text-sm text-slate-600 line-clamp-3">

                            <?php echo htmlspecialchars($contact['message']); ?>

                        </p>

                    </div>



                    <!-- ACTIONS -->

                    <div class="grid grid-cols-2 gap-3 mt-4">


                        <button
                            type="button"
                            onclick="openMessage(
                                <?php echo (int)$contact['id']; ?>,
                                <?php echo htmlspecialchars(json_encode($contact['name'])); ?>,
                                <?php echo htmlspecialchars(json_encode($contact['email'] ?? '')); ?>,
                                <?php echo htmlspecialchars(json_encode($contact['phone'] ?? '')); ?>,
                                <?php echo htmlspecialchars(json_encode($contact['subject'] ?? '')); ?>,
                                <?php echo htmlspecialchars(json_encode($contact['message'] ?? '')); ?>,
                                <?php echo htmlspecialchars(json_encode($contact['created_at'] ?? '')); ?>
                            )"
                            class="inline-flex items-center justify-center gap-2 px-4 py-3 rounded-xl bg-sky-100 text-sky-700 hover:bg-sky-200 font-semibold text-sm transition"
                        >

                            <i class="fa-solid fa-eye"></i>

                            View Details

                        </button>


                        <a
                            href="contacts.php?delete=<?php echo (int)$contact['id']; ?>"
                            onclick="return confirm('Are you sure you want to delete this message?')"
                            class="inline-flex items-center justify-center gap-2 px-4 py-3 rounded-xl bg-red-100 text-red-700 hover:bg-red-200 font-semibold text-sm transition"
                        >

                            <i class="fa-solid fa-trash"></i>

                            Delete

                        </a>

                    </div>

                </div>

            <?php endforeach; ?>


        <?php endif; ?>

    </div>

</main>



<!-- =========================
     VIEW MESSAGE MODAL
========================= -->

<div
    id="messageModal"
    class="fixed inset-0 bg-slate-900/60 hidden items-center justify-center p-4 z-50"
>

    <div
        class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto"
    >


        <!-- MODAL HEADER -->

        <div class="flex items-center justify-between p-6 border-b border-slate-200">

            <div>

                <h3 class="text-xl font-bold text-slate-900">

                    Message Details

                </h3>

                <p
                    id="modalId"
                    class="text-xs text-slate-500 mt-1"
                ></p>

            </div>


            <button
                type="button"
                onclick="closeMessage()"
                class="w-9 h-9 rounded-lg bg-slate-100 hover:bg-slate-200 text-slate-600 flex items-center justify-center"
            >

                <i class="fa-solid fa-xmark"></i>

            </button>

        </div>



        <!-- MODAL BODY -->

        <div class="p-6 space-y-5">


            <!-- NAME -->

            <div>

                <p class="text-xs font-bold uppercase text-slate-400 mb-1">
                    Name
                </p>

                <p
                    id="modalName"
                    class="font-semibold text-slate-900"
                ></p>

            </div>



            <!-- EMAIL -->

            <div>

                <p class="text-xs font-bold uppercase text-slate-400 mb-1">
                    Email
                </p>

                <a
                    id="modalEmail"
                    href="#"
                    class="text-sky-600 hover:text-sky-700 break-all"
                ></a>

            </div>



            <!-- PHONE -->

            <div id="modalPhoneBox">

                <p class="text-xs font-bold uppercase text-slate-400 mb-1">
                    Phone
                </p>

                <a
                    id="modalPhone"
                    href="#"
                    class="text-green-600 hover:text-green-700"
                ></a>

            </div>



            <!-- SUBJECT -->

            <div>

                <p class="text-xs font-bold uppercase text-slate-400 mb-1">
                    Subject
                </p>

                <p
                    id="modalSubject"
                    class="font-semibold text-slate-900"
                ></p>

            </div>



            <!-- MESSAGE -->

            <div>

                <p class="text-xs font-bold uppercase text-slate-400 mb-2">
                    Message
                </p>

                <div
                    id="modalMessage"
                    class="bg-slate-50 rounded-xl p-4 text-slate-700 leading-7 whitespace-pre-wrap"
                ></div>

            </div>



            <!-- DATE -->

            <div>

                <p class="text-xs font-bold uppercase text-slate-400 mb-1">
                    Received
                </p>

                <p
                    id="modalDate"
                    class="text-sm text-slate-600"
                ></p>

            </div>

        </div>



        <!-- MODAL FOOTER -->

        <div class="p-6 border-t border-slate-200 flex justify-end">

            <button
                type="button"
                onclick="closeMessage()"
                class="px-5 py-2.5 rounded-xl bg-slate-900 hover:bg-slate-800 text-white font-semibold transition"
            >

                Close

            </button>

        </div>

    </div>

</div>



<!-- =========================
     JAVASCRIPT
========================= -->

<script>

function openMessage(
    id,
    name,
    email,
    phone,
    subject,
    message,
    createdAt
) {

    document.getElementById("modalId").textContent =
        "Message #" + id;

    document.getElementById("modalName").textContent =
        name || "—";

    const emailElement =
        document.getElementById("modalEmail");

    emailElement.textContent =
        email || "—";

    if (email) {

        emailElement.href =
            "mailto:" + email;

    } else {

        emailElement.removeAttribute("href");

    }


    const phoneBox =
        document.getElementById("modalPhoneBox");

    const phoneElement =
        document.getElementById("modalPhone");

    if (phone) {

        phoneBox.classList.remove("hidden");

        phoneElement.textContent =
            phone;

        phoneElement.href =
            "tel:" + phone;

    } else {

        phoneBox.classList.add("hidden");

    }


    document.getElementById("modalSubject").textContent =
        subject || "General Inquiry";


    document.getElementById("modalMessage").textContent =
        message || "No message";


    let formattedDate = createdAt;

    if (createdAt) {

        const date = new Date(
            createdAt.replace(" ", "T")
        );

        if (!isNaN(date)) {

            formattedDate =
                date.toLocaleDateString(
                    "en-IN",
                    {
                        day: "2-digit",
                        month: "short",
                        year: "numeric"
                    }
                )
                +
                " • "
                +
                date.toLocaleTimeString(
                    "en-IN",
                    {
                        hour: "2-digit",
                        minute: "2-digit"
                    }
                );

        }

    }

    document.getElementById("modalDate").textContent =
        formattedDate || "—";


    const modal =
        document.getElementById("messageModal");

    modal.classList.remove("hidden");

    modal.classList.add("flex");

    document.body.classList.add("overflow-hidden");
}


function closeMessage() {

    const modal =
        document.getElementById("messageModal");

    modal.classList.add("hidden");

    modal.classList.remove("flex");

    document.body.classList.remove("overflow-hidden");
}


document.getElementById("messageModal")
    .addEventListener("click", function(event) {

        if (event.target === this) {

            closeMessage();

        }

    });


document.addEventListener("keydown", function(event) {

    if (event.key === "Escape") {

        closeMessage();

    }

});

</script>


<script src="../assets/js/admin.js"></script>
</body>
</html>