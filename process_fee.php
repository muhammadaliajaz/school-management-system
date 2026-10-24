<?php
require_once 'includes/db.php';
require_login();

if (isset($_GET['id']) && isset($_GET['action'])) {
    $id = $_GET['id'];
    $action = $_GET['action'];

    if ($action == 'delete') {
        try {
            $stmt = $conn->prepare("DELETE FROM fees WHERE id = ?");
            $stmt->bind_param("i", $id);
            $stmt->execute();
            $_SESSION['success'] = "Fee record deleted successfully!";
        } catch (Exception $e) {
            $_SESSION['error'] = "Error deleting fee record.";
        }
        header("Location: fees.php");
        exit();
    } elseif ($action == 'pay') {
        try {
            $payment_date = date('Y-m-d');
            $stmt = $conn->prepare("UPDATE fees SET status = 'Paid', payment_date = ? WHERE id = ?");
            $stmt->bind_param("si", $payment_date, $id);
            $stmt->execute();
            $_SESSION['success'] = "Fee marked as Paid!";
        } catch (Exception $e) {
            $_SESSION['error'] = "Error updating status.";
        }
        header("Location: fees.php");
        exit();
    } elseif ($action == 'receipt') {
        // Simple receipt display
        include 'includes/header.php';
        
        $stmt = $conn->prepare("
            SELECT f.*, s.first_name, s.last_name, s.email, s.phone, s.address 
            FROM fees f 
            JOIN students s ON f.student_id = s.id 
            WHERE f.id = ?
        ");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $fee = $stmt->get_result()->fetch_assoc();

        if ($fee && $fee['status'] == 'Paid') {
            ?>
            <div class="card p-5 mx-auto" style="max-width: 600px;">
                <div class="text-center mb-4 border-bottom pb-3">
                    <i class="fa-solid fa-school fa-3x text-primary mb-3"></i>
                    <h2>School Management System</h2>
                    <h4 class="text-muted">Payment Receipt</h4>
                </div>
                <div class="row mb-4">
                    <div class="col-6">
                        <strong>Student:</strong><br>
                        <?php echo htmlspecialchars($fee['first_name'] . ' ' . $fee['last_name']); ?><br>
                        <small>Student ID: <?php echo htmlspecialchars($fee['id']); ?></small><br>
                        <?php echo htmlspecialchars($fee['email']); ?><br>
                        <?php echo htmlspecialchars($fee['phone']); ?>
                    </div>
                    <div class="col-6 text-end">
                        <strong>Receipt #:</strong> INV-<?php echo str_pad($fee['id'], 4, '0', STR_PAD_LEFT); ?><br>
                        <strong>Date Paid:</strong> <?php echo date('F d, Y', strtotime($fee['payment_date'])); ?>
                    </div>
                </div>
                <table class="table table-bordered mb-4">
                    <thead class="bg-light">
                        <tr>
                            <th>Description</th>
                            <th class="text-end">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>School Fee Payment</td>
                            <td class="text-end">Rs. <?php echo number_format($fee['amount'], 2); ?></td>
                        </tr>
                        <tr>
                            <td class="text-end fw-bold">Total</td>
                            <td class="text-end fw-bold text-success">Rs. <?php echo number_format($fee['amount'], 2); ?></td>
                        </tr>
                    </tbody>
                </table>
                <div class="text-center mt-5">
                    <p class="text-muted">Thank you for your payment.</p>
                    <button onclick="window.print()" class="btn btn-primary d-print-none"><i class="fa-solid fa-print me-1"></i> Print Receipt</button>
                    <a href="fees.php" class="btn btn-secondary d-print-none ms-2">Back to Fees</a>
                </div>
            </div>
            
            <style>
                @media print {
                    .navbar, footer, .d-print-none { display: none !important; }
                    body { background: white; }
                    .card { border: none; box-shadow: none; }
                }
            </style>
            <?php
        } else {
            echo "<div class='alert alert-danger'>Receipt is not available or fee is not paid yet.</div>";
            echo "<a href='fees.php' class='btn btn-secondary mt-3'>Back</a>";
        }
        include 'includes/footer.php';
        exit();
    }
}

header("Location: fees.php");
exit();
?>
