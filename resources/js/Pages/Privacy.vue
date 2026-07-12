<template>
    <PublicLayout>

        <Head>
            <title>Privacy Policy | Kambikutan</title>
            <meta head-key="description" name="description" content="Read the Privacy Policy of Kambikutan to learn how we collect, use, and protect your information when you visit our website.">
            <link head-key="canonical" rel="canonical" href="https://kambikutan.com/privacy-policy">
            <meta head-key="og:title" property="og:title" content="Privacy Policy – Kambikutan">
            <meta head-key="og:description" property="og:description" content="How Kambikutan collects, uses, and protects your information.">
            <meta head-key="og:url" property="og:url" content="https://kambikutan.com/privacy-policy">
            <meta head-key="og:type" property="og:type" content="website">
        </Head>

        <main class="max-w-3xl mx-auto px-6 py-12">
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900 tracking-tight">Privacy Policy</h1>
                <p class="mt-2 text-sm text-gray-400">Last updated: July 12, 2026</p>
            </div>

            <div class="prose prose-gray max-w-none text-gray-600">
                <p>
                    This Privacy Policy describes how kambikutan.com ("we", "us", or "our") collects,
                    uses, and shares information when you visit and use our website (the "Site"). By
                    using the Site, you agree to the practices described in this policy.
                </p>

                <h2>Information We Collect</h2>
                <p>We may collect the following types of information:</p>
                <ul>
                    <li>
                        <strong>Information you provide.</strong> When you submit a story, contact us,
                        or otherwise interact with the Site, you may voluntarily provide information
                        such as your name and email address.
                    </li>
                    <li>
                        <strong>Automatically collected information.</strong> Like most websites, we
                        may automatically collect certain information about your device and usage,
                        including your IP address, browser type, pages visited, and the date and time
                        of your visit.
                    </li>
                    <li>
                        <strong>Cookies.</strong> We may use cookies and similar technologies to
                        operate the Site, remember your preferences, and understand how the Site is
                        used.
                    </li>
                </ul>

                <h2>How We Use Information</h2>
                <p>We use the information we collect to:</p>
                <ul>
                    <li>Operate, maintain, and improve the Site and its content;</li>
                    <li>Respond to your messages, submissions, and requests;</li>
                    <li>Analyze usage trends and measure the performance of our content;</li>
                    <li>Protect the security and integrity of the Site.</li>
                </ul>

                <h2>Cookies and Analytics</h2>
                <p>
                    We may use third-party analytics services to help us understand how visitors use
                    the Site. These services may set their own cookies and collect information in
                    accordance with their own privacy policies. You can control or disable cookies
                    through your browser settings, though some features of the Site may not function
                    properly as a result.
                </p>

                <h2>Third-Party Links</h2>
                <p>
                    The Site may contain links to third-party websites. We are not responsible for the
                    privacy practices or content of those websites. We encourage you to review the
                    privacy policies of any third-party sites you visit.
                </p>

                <h2>Data Retention</h2>
                <p>
                    We retain information for as long as necessary to fulfil the purposes described in
                    this policy, unless a longer retention period is required or permitted by law.
                </p>

                <h2>Children's Privacy</h2>
                <p>
                    The Site is intended for adults and is not directed at children. We do not
                    knowingly collect personal information from children.
                </p>

                <h2>Your Rights</h2>
                <p>
                    Depending on your location, you may have rights regarding your personal
                    information, such as the right to access, correct, or delete it. To exercise any
                    such rights, please contact us using the details below.
                </p>

                <h2>Changes to This Policy</h2>
                <p>
                    We may update this Privacy Policy from time to time. Any changes will be posted on
                    this page with an updated "Last updated" date.
                </p>

                <h2>Contact Us</h2>
                <p>
                    If you have any questions about this Privacy Policy, please
                    <Link :href="route('contact')" class="text-indigo-600 hover:text-indigo-700">contact us</Link>.
                </p>
            </div>
        </main>

    </PublicLayout>
</template>

<script setup>
import { Head, Link } from '@inertiajs/vue3';
import PublicLayout from '@/Components/PublicLayout.vue';
</script>
