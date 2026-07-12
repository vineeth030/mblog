<template>
    <PublicLayout>

        <Head>
            <title>Terms and Conditions | Kambikutan</title>
            <meta head-key="description" name="description" content="Read the Terms and Conditions for using Kambikutan. By accessing our website you agree to these terms.">
            <link head-key="canonical" rel="canonical" href="https://kambikutan.com/terms-and-conditions">
            <meta head-key="og:title" property="og:title" content="Terms and Conditions – Kambikutan">
            <meta head-key="og:description" property="og:description" content="The terms that govern your use of the Kambikutan website.">
            <meta head-key="og:url" property="og:url" content="https://kambikutan.com/terms-and-conditions">
            <meta head-key="og:type" property="og:type" content="website">
        </Head>

        <main class="max-w-3xl mx-auto px-6 py-12">
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900 tracking-tight">Terms and Conditions</h1>
                <p class="mt-2 text-sm text-gray-400">Last updated: July 12, 2026</p>
            </div>

            <div class="prose prose-gray max-w-none text-gray-600">
                <p>
                    These Terms and Conditions ("Terms") govern your access to and use of
                    kambikutan.com (the "Site"). By accessing or using the Site, you agree to be bound
                    by these Terms. If you do not agree with any part of these Terms, please do not use
                    the Site.
                </p>

                <h2>Eligibility</h2>
                <p>
                    The Site and its content are intended for adults only. By using the Site, you
                    represent that you are of legal age in your jurisdiction to view its content.
                </p>

                <h2>Use of the Site</h2>
                <p>You agree to use the Site only for lawful purposes. You agree not to:</p>
                <ul>
                    <li>Use the Site in any way that violates any applicable law or regulation;</li>
                    <li>Attempt to gain unauthorized access to any part of the Site or its systems;</li>
                    <li>Interfere with or disrupt the operation, security, or availability of the Site;</li>
                    <li>Copy, reproduce, or redistribute content from the Site without permission.</li>
                </ul>

                <h2>Intellectual Property</h2>
                <p>
                    Unless otherwise stated, the content on the Site, including text, graphics, logos,
                    and design, is owned by or licensed to us and is protected by applicable
                    intellectual property laws. You may not use, reproduce, or distribute any content
                    without our prior written consent.
                </p>

                <h2>User Submissions</h2>
                <p>
                    If you submit a story or any other content to the Site, you confirm that you have
                    the right to do so and grant us a non-exclusive, royalty-free license to use,
                    publish, edit, and display that content on the Site. We reserve the right to
                    review, edit, or remove any submitted content at our discretion.
                </p>

                <h2>Disclaimer</h2>
                <p>
                    The Site and its content are provided on an "as is" and "as available" basis
                    without warranties of any kind, whether express or implied. We do not guarantee
                    that the Site will be uninterrupted, error-free, or free of harmful components.
                </p>

                <h2>Limitation of Liability</h2>
                <p>
                    To the fullest extent permitted by law, we shall not be liable for any indirect,
                    incidental, or consequential damages arising from your use of, or inability to use,
                    the Site.
                </p>

                <h2>Third-Party Links</h2>
                <p>
                    The Site may contain links to third-party websites. We are not responsible for the
                    content, policies, or practices of any third-party websites.
                </p>

                <h2>Changes to These Terms</h2>
                <p>
                    We may revise these Terms at any time. Any changes will be posted on this page with
                    an updated "Last updated" date. Your continued use of the Site after changes are
                    posted constitutes your acceptance of the revised Terms.
                </p>

                <h2>Contact Us</h2>
                <p>
                    If you have any questions about these Terms, please
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
