<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Assistance Request Update - Void Clearance System</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f4f4; margin: 0; padding: 20px; }
        .container { max-width: 500px; margin: 0 auto; background: white; border-radius: 10px; overflow: hidden; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .header { background: linear-gradient(135deg, #f59e0b, #d97706); padding: 20px; text-align: center; }
        .header h1 { color: white; margin: 0; font-size: 24px; }
        .content { padding: 30px; }
        .request-box { background: #fef3c7; padding: 15px; border-radius: 8px; margin: 15px 0; border-left: 4px solid #f59e0b; }
        .response-box { background: #dbeafe; padding: 15px; border-radius: 8px; margin: 15px 0; border-left: 4px solid #3b82f6; }
        .status-resolved { color: #10b981; }
        .status-progress { color: #3b82f6; }
        .button { display: inline-block; background: #f59e0b; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; margin-top: 15px; }
        .footer { background: #f8f9fa; padding: 20px; text-align: center; font-size: 12px; color: #666; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🎫 Assistance Request Update</h1>
        </div>
        <div class="content">
            <p>Dear <strong><?php echo e($student->first_name); ?> <?php echo e($student->last_name); ?></strong>,</p>
            
            <p>Your assistance request (#<?php echo e($request->id); ?>) has been updated.</p>
            
            <div class="request-box">
                <strong>📋 Request Details:</strong><br>
                <p><strong>Type:</strong> <?php echo e(str_replace('_', ' ', ucfirst($request->request_type))); ?></p>
                <p><strong>Description:</strong> <?php echo e($request->description); ?></p>
            </div>
            
            <div class="response-box">
                <strong>💬 Admin Response:</strong><br>
                <p><?php echo e($admin_notes); ?></p>
            </div>
            
            <div class="status-<?php echo e($request->status); ?>">
                <strong>Status:</strong> 
                <?php if($request->status == 'resolved'): ?> ✅ RESOLVED
                <?php elseif($request->status == 'in_progress'): ?> 🔄 IN PROGRESS
                <?php elseif($request->status == 'pending'): ?> ⏳ PENDING
                <?php else: ?> ❌ CANCELLED <?php endif; ?>
            </div>
            
            <?php if($request->status == 'resolved'): ?>
                <p style="margin-top: 15px; color: #10b981;">✓ Your request has been resolved. If you have further concerns, please submit a new request.</p>
            <?php endif; ?>
            
            <div style="text-align: center;">
                <a href="<?php echo e(url('/student/assistance')); ?>" class="button">View My Requests</a>
            </div>
        </div>
        <div class="footer">
            <p>Void Clearance System - Support Team</p>
            <p>© <?php echo e(date('Y')); ?> All rights reserved.</p>
        </div>
    </div>
</body>
</html><?php /**PATH /home/vol15_6/infinityfree.com/if0_42013478/voidclearancesystem.gt.tc/htdocs/resources/views/emails/assistance-response.blade.php ENDPATH**/ ?>