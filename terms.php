<?php include "includes/header.php"; ?>

<style>
:root {
    --bg-color: #ffffff;
    --card-bg: #f8f9fa;
    --text-color: #222222;
    --border-color: #dddddd;
}

@media (prefers-color-scheme: dark) {
    :root {
        --bg-color: #000000;
        --card-bg: #111111;
        --text-color: #ffffff;
        --border-color: #333333;
    }
}

.terms-container {
    max-width: 1000px;
    margin: 30px auto;
    padding: 20px;
    background: var(--bg-color);
    color: var(--text-color);
}

.about-card,
.about-highlight {
    background: var(--card-bg);
    color: var(--text-color);
    border: 1px solid var(--border-color);
    border-radius: 12px;
    padding: 20px;
    margin-bottom: 20px;
}

.about-card h2,
.about-highlight h2,
.terms-container h1 {
    color: var(--text-color);
}

.about-card p,
.about-highlight p,
.about-list li {
    color: var(--text-color);
    line-height: 1.7;
}

.about-list {
    padding-left: 20px;
}
</style>

<div class="terms-container">

    <h1>Terms & Conditions</h1>
    <p>Please read these terms carefully before using Global Trade Hub.</p>

    <div class="about-card">
        <h2>1. Acceptance of Terms</h2>
        <p>
            By accessing or using Global Trade Hub, you agree to be bound by these Terms and Conditions.
            If you do not agree with any part of these terms, please discontinue use of the platform.
        </p>
    </div>

    <div class="about-card">
        <h2>2. User Accounts</h2>
        <p>
            Users are responsible for maintaining the confidentiality of their account credentials
            and for all activities conducted under their accounts.
        </p>
    </div>

    <div class="about-card">
        <h2>3. Seller Responsibilities</h2>
        <p>
            Sellers must provide accurate product information, genuine products, and truthful pricing.
            Fraudulent listings, misleading descriptions, or prohibited items may result in suspension
            or permanent removal.
        </p>
    </div>

    <div class="about-card">
        <h2>4. Buyer Responsibilities</h2>
        <p>
            Buyers are expected to provide accurate shipping information, comply with platform rules,
            and complete transactions honestly.
        </p>
    </div>

    <div class="about-card">
        <h2>5. Payments & Transactions</h2>
        <p>
            All payments processed through the platform are subject to verification and security checks.
            Global Trade Hub reserves the right to review suspicious transactions.
        </p>
    </div>

    <div class="about-card">
        <h2>6. Withdrawals</h2>
        <p>
            Sellers may request withdrawals of available balances. Withdrawal requests may be reviewed
            before processing and may be subject to service charges and verification procedures.
        </p>
    </div>

    <div class="about-card">
        <h2>7. Prohibited Activities</h2>
        <ul class="about-list">
            <li>Fraudulent transactions</li>
            <li>Sale of illegal products</li>
            <li>Identity theft or impersonation</li>
            <li>Uploading harmful content</li>
            <li>Manipulating ratings or reviews</li>
            <li>Unauthorized access to user accounts</li>
        </ul>
    </div>

    <div class="about-card">
        <h2>8. Intellectual Property</h2>
        <p>
            All trademarks, logos, designs, and platform content belong to Global Trade Hub unless
            otherwise stated. Unauthorized use is prohibited.
        </p>
    </div>

    <div class="about-card">
        <h2>9. Limitation of Liability</h2>
        <p>
            Global Trade Hub acts as a marketplace connecting buyers and sellers. We are not liable
            for disputes, losses, or delivery issues arising directly between users.
        </p>
    </div>

    <div class="about-card">
        <h2>10. Account Suspension</h2>
        <p>
            We reserve the right to suspend or terminate accounts found violating these terms,
            engaging in fraudulent activities, or compromising platform security.
        </p>
    </div>

    <div class="about-card">
        <h2>11. Changes to Terms</h2>
        <p>
            Global Trade Hub may update these Terms and Conditions from time to time. Continued use
            of the platform constitutes acceptance of any revised terms.
        </p>
    </div>

    <div class="about-highlight">
        <h2>Agreement</h2>
        <p>
            By creating an account or using Global Trade Hub, you acknowledge that you have read,
            understood, and agreed to these Terms and Conditions.
        </p>
    </div>

</div>

<?php include "includes/footer.php"; ?>
