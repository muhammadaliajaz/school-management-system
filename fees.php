<?php
require_once 'includes/db.php';
include 'includes/header.php';

$result = $conn->query("
    SELECT f.*, s.first_name, s.last_name 
    FROM fees f 
    JOIN students s ON f.student_id = s.id 
    ORDER BY f.id ASC
");
$fees = $result->fetch_all(MYSQLI_ASSOC);
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold">Fee Management</h2>
    <a href="add_fee.php" class="btn btn-warning fw-bold text-dark"><i class="fa-solid fa-file-invoice-dollar me-1"></i> Add Fee Record</a>
</div>



<div class="card p-4">
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead>
                <tr>
                    <th>Invoice ID</th>
                    <th>Student Name</th>
                    <th>Amount (Rs.)</th>
                    <th>Due Date</th>
                    <th>Status</th>
                    <th>Payment Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if(count($fees) > 0): ?>
                    <?php foreach($fees as $f): ?>
                    <tr>
                        <td>#INV-<?php echo str_pad($f['id'], 4, '0', STR_PAD_LEFT); ?></td>
                        <td><?php echo htmlspecialchars($f['first_name'] . ' ' . $f['last_name']); ?></td>
                        <td class="fw-bold text-success">Rs. <?php echo number_format($f['amount'], 2); ?></td>
                        <td><?php echo date('M d, Y', strtotime($f['due_date'])); ?></td>
                        <td>
                            <?php if($f['status'] == 'Paid'): ?>
                                <span class="badge bg-success">Paid</span>
                            <?php else: ?>
                                <span class="badge bg-warning text-dark">Pending</span>
                            <?php endif; ?>
                        </td>
                        <td><?php echo $f['payment_date'] ? date('M d, Y', strtotime($f['payment_date'])) : '-'; ?></td>
                        <td>
                            <?php if($f['status'] == 'Pending'): ?>
                                <a href="process_fee.php?id=<?php echo $f['id']; ?>&action=pay" class="btn btn-sm btn-success" onclick="return confirm('Mark this fee as Paid?');"><i class="fa-solid fa-check"></i> Pay</a>
                            <?php else: ?>
                                <a href="process_fee.php?id=<?php echo $f['id']; ?>&action=receipt" class="btn btn-sm btn-outline-primary"><i class="fa-solid fa-receipt"></i> Receipt</a>
                            <?php endif; ?>
                            <a href="process_fee.php?id=<?php echo $f['id']; ?>&action=delete" class="btn btn-sm btn-danger" onclick="return confirm('Delete this fee record?');"><i class="fa-solid fa-trash"></i></a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="7" class="text-center text-muted">No fee records found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include 'includes/footer.php'; ?>
