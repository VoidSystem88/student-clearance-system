<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Login - Clearance System</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="container mx-auto px-4 py-20">
        <div class="max-w-md mx-auto bg-white rounded-lg shadow-md p-8">
            <h2 class="text-2xl font-bold mb-6 text-center">Department Staff Login</h2>
            
            <?php if(session('error')): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-2 rounded mb-4">
                    <?php echo e(session('error')); ?>

                </div>
            <?php endif; ?>
            
            <?php if($errors->any()): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-2 rounded mb-4">
                    <?php echo e($errors->first()); ?>

                </div>
            <?php endif; ?>
            
            <form method="POST" action="<?php echo e(route('staff.login')); ?>">
                <?php echo csrf_field(); ?>
                <div class="mb-4">
                    <label class="block text-gray-700 mb-2">Staff Email</label>
                    <input type="email" name="email" class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:border-blue-500" required>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 mb-2">Password</label>
                    <input type="password" name="password" class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:border-blue-500" required>
                </div>
                <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700">Login</button>
                <p class="mt-4 text-center text-gray-600">
                    <a href="<?php echo e(url('/')); ?>" class="text-blue-600 hover:underline">← Back to Home</a>
                </p>
            </form>
        </div>
    </div>
</body>
</html><?php /**PATH /home/vol15_6/infinityfree.com/if0_42013478/voidclearancesystem.gt.tc/htdocs/resources/views/staff/login.blade.php ENDPATH**/ ?>