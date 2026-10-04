<?php

require_once "../includes/auth.php";
require_once "../config/database.php";

$message = "";
$error = "";

/* =========================
   DELETE BLOG
========================= */

if (isset($_GET['delete'])) {

    $id = (int) $_GET['delete'];

    if ($id >
 0) {

        $stmt = $pdo->prepare("SELECT image FROM blogs WHERE id = ?");
        $stmt->execute([$id]);
        $blog = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($blog) {

            $deleteStmt = $pdo->prepare("DELETE FROM blogs WHERE id = ?");
            $deleteStmt->execute([$id]);

            if (!empty($blog['image'])) {

                $imagePath = "../uploads/" . $blog['image'];

                if (file_exists($imagePath)) {
                    unlink($imagePath);
                }
            }
        }
    }

    header("Location: blogs.php");
    exit;
}


/* =========================
   ADD BLOG
========================= */

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['add_blog'])) {

    $title = trim($_POST['title'] ?? "");
    $slug = trim($_POST['slug'] ?? "");
    $content = trim($_POST['content'] ?? "");
    $author = trim($_POST['author'] ?? "");
    $status = $_POST['status'] ?? "published";

    if ($title === "" || $slug === "" || $content === "") {

        $error = "Title, slug and content are required.";

    } else {

        /* Check slug */

        $check = $pdo->prepare("SELECT id FROM blogs WHERE slug = ?");
        $check->execute([$slug]);

        if ($check->fetch()) {

            $error = "This slug already exists.";

        } else {

            $imageName = "";

            /* Image Upload */

            if (
                isset($_FILES['image']) &&
                $_FILES['image']['error'] === UPLOAD_ERR_OK
            ) {

                $allowedTypes = [
                    "image/jpeg",
                    "image/png",
                    "image/webp"
                ];

                $fileType = mime_content_type($_FILES['image']['tmp_name']);

                if (!in_array($fileType, $allowedTypes)) {

                    $error = "Only JPG, PNG and WEBP images are allowed.";

                } else {

                    $extension = pathinfo(
                        $_FILES['image']['name'],
                        PATHINFO_EXTENSION
                    );

                    $imageName = uniqid("blog_", true) . "." . $extension;

                    $uploadPath = "../uploads/" . $imageName;

                    if (!move_uploaded_file(
                        $_FILES['image']['tmp_name'],
                        $uploadPath
                    )) {

                        $error = "Image upload failed.";
                    }
                }
            }


            /* Insert */

            if ($error === "") {

                $stmt = $pdo->prepare("
                    INSERT INTO blogs
                    (title, slug, content, image, author, status)
                    VALUES (?, ?, ?, ?, ?, ?)
                ");

                $stmt->execute([
                    $title,
                    $slug,
                    $content,
                    $imageName,
                    $author,
                    $status
                ]);

                header("Location: blogs.php");
                exit;
            }
        }
    }
}


/* =========================
   FETCH BLOGS
========================= */

$stmt = $pdo->query("
    SELECT *
    FROM blogs
    ORDER BY id DESC
");

$blogs = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Blogs - TravelGo Admin</title>

    <script src="https://cdn.tailwindcss.com"></script>

    <link rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

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


    <!-- TITLE -->

    <div class="mb-6">

        <h2 class="text-3xl font-bold text-slate-900">

            Blog Management

        </h2>

        <p class="text-slate-500 mt-1">

            Add and manage TravelGo blog posts.

        </p>

    </div>



    <!-- =========================
         ERROR
    ========================= -->

    <?php if ($error !== ""): ?>

        <div class="mb-6 p-4 rounded-xl
                    bg-red-50
                    border border-red-200
                    text-red-700">

            <i class="fa-solid fa-circle-exclamation mr-2"></i>

            <?php echo htmlspecialchars($error); ?>

        </div>

    <?php endif; ?>



    <!-- =========================
         ADD BLOG FORM
    ========================= -->

    <div class="bg-white rounded-2xl
                shadow-sm
                border border-slate-200
                p-6 mb-8">

        <div class="flex items-center gap-3 mb-6">

            <div class="w-11 h-11
                        rounded-xl
                        bg-sky-100
                        text-sky-600
                        flex items-center
                        justify-center">

                <i class="fa-solid fa-pen-to-square"></i>

            </div>

            <div>

                <h3 class="text-xl font-bold">

                    Add New Blog

                </h3>

                <p class="text-sm text-slate-500">

                    Create a new travel blog post.

                </p>

            </div>

        </div>


        <form method="POST"
              enctype="multipart/form-data"
              class="space-y-5">


            <!-- TITLE -->

            <div>

                <label class="block text-sm font-semibold mb-2">

                    Blog Title

                </label>

                <input
                    type="text"
                    name="title"
                    id="title"
                    required
                    placeholder="Example: 10 Best Places to Visit in Rajasthan"
                    class="w-full px-4 py-3
                           rounded-xl
                           border border-slate-300
                           focus:outline-none
                           focus:ring-2
                           focus:ring-sky-500">

            </div>



            <!-- SLUG -->

            <div>

                <label class="block text-sm font-semibold mb-2">

                    Slug

                </label>

                <input
                    type="text"
                    name="slug"
                    id="slug"
                    required
                    placeholder="10-best-places-to-visit-in-rajasthan"
                    class="w-full px-4 py-3
                           rounded-xl
                           border border-slate-300
                           focus:outline-none
                           focus:ring-2
                           focus:ring-sky-500">

                <p class="text-xs text-slate-400 mt-1">

                    Use lowercase letters and hyphens.

                </p>

            </div>



            <!-- AUTHOR -->

            <div>

                <label class="block text-sm font-semibold mb-2">

                    Author

                </label>

                <input
                    type="text"
                    name="author"
                    placeholder="TravelGo Team"
                    class="w-full px-4 py-3
                           rounded-xl
                           border border-slate-300
                           focus:outline-none
                           focus:ring-2
                           focus:ring-sky-500">

            </div>



            <!-- CONTENT -->

            <div>

                <label class="block text-sm font-semibold mb-2">

                    Blog Content

                </label>

                <textarea
                    name="content"
                    rows="8"
                    required
                    placeholder="Write your complete blog content here..."
                    class="w-full px-4 py-3
                           rounded-xl
                           border border-slate-300
                           focus:outline-none
                           focus:ring-2
                           focus:ring-sky-500"></textarea>

            </div>



            <!-- IMAGE + STATUS -->

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">


                <!-- IMAGE -->

                <div>

                    <label class="block text-sm font-semibold mb-2">

                        Featured Image

                    </label>

                    <input
                        type="file"
                        name="image"
                        accept=".jpg,.jpeg,.png,.webp"
                        class="w-full px-4 py-3
                               rounded-xl
                               border border-slate-300
                               bg-white">

                    <p class="text-xs text-slate-400 mt-1">

                        JPG, PNG or WEBP

                    </p>

                </div>



                <!-- STATUS -->

                <div>

                    <label class="block text-sm font-semibold mb-2">

                        Status

                    </label>

                    <select
                        name="status"
                        class="w-full px-4 py-3
                               rounded-xl
                               border border-slate-300
                               bg-white
                               focus:outline-none
                               focus:ring-2
                               focus:ring-sky-500">

                        <option value="published">
                            Published
                        </option>

                        <option value="draft">
                            Draft
                        </option>

                    </select>

                </div>

            </div>



            <!-- BUTTON -->

            <button
                type="submit"
                name="add_blog"
                class="inline-flex items-center gap-2
                       px-6 py-3
                       rounded-xl
                       bg-sky-500
                       hover:bg-sky-600
                       text-white
                       font-bold
                       transition">

                <i class="fa-solid fa-plus"></i>

                Add Blog

            </button>

        </form>

    </div>



    <!-- =========================
         BLOG LIST
    ========================= -->

    <div class="bg-white rounded-2xl
                shadow-sm
                border border-slate-200
                overflow-hidden">

        <div class="p-6 border-b">

            <h3 class="text-xl font-bold">

                All Blog Posts

            </h3>

            <p class="text-sm text-slate-500 mt-1">

                Total:
                <?php echo count($blogs); ?>
                blog posts

            </p>

        </div>



        <?php if (empty($blogs)): ?>

            <div class="py-16 text-center">

                <div class="w-16 h-16
                            mx-auto mb-4
                            rounded-full
                            bg-slate-100
                            flex items-center
                            justify-center">

                    <i class="fa-solid fa-newspaper
                              text-2xl
                              text-slate-400"></i>

                </div>

                <h3 class="font-bold text-lg">

                    No blogs yet

                </h3>

                <p class="text-slate-500 mt-1">

                    Add your first blog post above.

                </p>

            </div>


        <?php else: ?>


            <!-- DESKTOP -->

            <div class="hidden md:block overflow-x-auto">

                <table class="w-full">

                    <thead class="bg-slate-50 border-b">

                        <tr>

                            <th class="text-left px-6 py-4">
                                Blog
                            </th>

                            <th class="text-left px-6 py-4">
                                Author
                            </th>

                            <th class="text-left px-6 py-4">
                                Status
                            </th>

                            <th class="text-left px-6 py-4">
                                Date
                            </th>

                            <th class="text-right px-6 py-4">
                                Action
                            </th>

                        </tr>

                    </thead>


                    <tbody class="divide-y">

                        <?php foreach ($blogs as $blog): ?>

                            <tr class="hover:bg-slate-50">


                                <!-- BLOG -->

                                <td class="px-6 py-4">

                                    <div class="flex items-center gap-4">

                                        <?php if (!empty($blog['image'])): ?>

                                            <img
                                                src="../uploads/<?php echo htmlspecialchars($blog['image']); ?>"
                                                class="w-20 h-14
                                                       object-cover
                                                       rounded-lg">

                                        <?php else: ?>

                                            <div class="w-20 h-14
                                                        rounded-lg
                                                        bg-slate-100
                                                        flex items-center
                                                        justify-center">

                                                <i class="fa-solid fa-image
                                                          text-slate-400"></i>

                                            </div>

                                        <?php endif; ?>


                                        <div>

                                            <p class="font-semibold max-w-xs">

                                                <?php
                                                echo htmlspecialchars(
                                                    $blog['title']
                                                );
                                                ?>

                                            </p>

                                            <p class="text-xs text-slate-400">

                                                /<?php
                                                echo htmlspecialchars(
                                                    $blog['slug']
                                                );
                                                ?>

                                            </p>

                                        </div>

                                    </div>

                                </td>


                                <!-- AUTHOR -->

                                <td class="px-6 py-4 text-sm">

                                    <?php
                                    echo !empty($blog['author'])
                                        ? htmlspecialchars($blog['author'])
                                        : "TravelGo";
                                    ?>

                                </td>


                                <!-- STATUS -->

                                <td class="px-6 py-4">

                                    <?php if ($blog['status'] === 'published'): ?>

                                        <span class="inline-flex
                                                     px-3 py-1
                                                     rounded-full
                                                     bg-green-100
                                                     text-green-700
                                                     text-xs
                                                     font-bold">

                                            Published

                                        </span>

                                    <?php else: ?>

                                        <span class="inline-flex
                                                     px-3 py-1
                                                     rounded-full
                                                     bg-yellow-100
                                                     text-yellow-700
                                                     text-xs
                                                     font-bold">

                                            Draft

                                        </span>

                                    <?php endif; ?>

                                </td>


                                <!-- DATE -->

                                <td class="px-6 py-4 text-sm text-slate-500">

                                    <?php
                                    echo date(
                                        "d M Y",
                                        strtotime($blog['created_at'])
                                    );
                                    ?>

                                </td>


                                <!-- ACTION -->

                                <td class="px-6 py-4 text-right">

                                    <a
                                        href="blogs.php?delete=<?php echo (int)$blog['id']; ?>"
                                        onclick="return confirm('Are you sure you want to delete this blog?');"
                                        class="inline-flex
                                               items-center gap-2
                                               px-3 py-2
                                               rounded-lg
                                               bg-red-50
                                               text-red-600
                                               hover:bg-red-100
                                               text-sm
                                               font-semibold">

                                        <i class="fa-solid fa-trash"></i>

                                        Delete

                                    </a>

                                </td>

                            </tr>

                        <?php endforeach; ?>

                    </tbody>

                </table>

            </div>



            <!-- MOBILE -->

            <div class="md:hidden divide-y">

                <?php foreach ($blogs as $blog): ?>

                    <div class="p-5">


                        <?php if (!empty($blog['image'])): ?>

                            <img
                                src="../uploads/<?php echo htmlspecialchars($blog['image']); ?>"
                                class="w-full h-48
                                       object-cover
                                       rounded-xl mb-4">

                        <?php endif; ?>


                        <h3 class="font-bold text-lg">

                            <?php
                            echo htmlspecialchars($blog['title']);
                            ?>

                        </h3>


                        <p class="text-sm text-slate-500 mt-2">

                            <?php
                            echo htmlspecialchars(
                                mb_substr(
                                    strip_tags($blog['content']),
                                    0,
                                    120
                                )
                            );
                            ?>...

                        </p>


                        <div class="flex items-center
                                    justify-between
                                    mt-4">


                            <?php if ($blog['status'] === 'published'): ?>

                                <span class="px-3 py-1
                                             rounded-full
                                             bg-green-100
                                             text-green-700
                                             text-xs
                                             font-bold">

                                    Published

                                </span>

                            <?php else: ?>

                                <span class="px-3 py-1
                                             rounded-full
                                             bg-yellow-100
                                             text-yellow-700
                                             text-xs
                                             font-bold">

                                    Draft

                                </span>

                            <?php endif; ?>


                            <a
                                href="blogs.php?delete=<?php echo (int)$blog['id']; ?>"
                                onclick="return confirm('Are you sure you want to delete this blog?');"
                                class="text-red-600 font-semibold text-sm">

                                <i class="fa-solid fa-trash mr-1"></i>

                                Delete

                            </a>

                        </div>

                    </div>

                <?php endforeach; ?>

            </div>


        <?php endif; ?>

    </div>



    <!-- BACK -->

    <div class="mt-6">

        <a href="dashboard.php"
           class="inline-flex items-center gap-2
                  text-sky-600
                  hover:text-sky-700
                  font-semibold">

            <i class="fa-solid fa-arrow-left"></i>

            Back to Dashboard

        </a>

    </div>

</main>



<!-- =========================
     AUTO SLUG
========================= -->

<script>

const titleInput = document.getElementById("title");
const slugInput = document.getElementById("slug");

titleInput.addEventListener("input", function () {

    let slug = this.value
        .toLowerCase()
        .trim()
        .replace(/[^a-z0-9\s-]/g, "")
        .replace(/\s+/g, "-")
        .replace(/-+/g, "-");

    slugInput.value = slug;

});

</script>


<script src="../assets/js/admin.js"></script>
</body>
</html>