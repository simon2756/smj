<?php
/**
 *  @author Felix Betz
 *  @date 02.03.2026
 *
 * Code-Snippet "NewsletterAnmeldung" (Plugin Code Snippets, Typ php, überall aktiv).
 * Wird ausgeführt sobald eine Email erfolgreich mit dem Contact Form 7-Formular versendet wurde.
 * Liest 'Vorname'und 'Nachname' sowie 'Mail' und 'Newsletter' aus den Formulardaten.
 * Falls 'Newsletter' angekreuz ist, wird die Person mit Mail und Name automatisch in die Liste 1
 * des Newsletter-Plugins eingetragen, sofern sie noch nicht abonniert ist.
 */
add_action('wpcf7_mail_sent', function($cf7) {

    $submission = WPCF7_Submission::get_instance();
    if (!$submission) {
        return;
    }

    // Gesendete Formulardaten abrufen
    $data = $submission->get_posted_data();

    // Alle Keys des Arrays in Kleinbuchstaben umwandeln
    $data = array_change_key_case($data, CASE_LOWER);

    // Newsletter-Feld auslesen – CF7 liefert Checkbox-Werte als Array
    $newsletter_raw = is_array($data['newsletter']) ? $data['newsletter'][0] : ($data['newsletter'] ?? '');
    $newsletter_wert = sanitize_text_field($newsletter_raw);

    // Prüfen ob Newsletter-Feld auf 'ja' gesetzt ist
    if (strtolower($newsletter_wert) !== 'ja') {
        return;
    }

    // E-Mail-Adresse bereinigen und validieren
    $email = sanitize_email($data['email'] ?? $data['mail'] ?? '');
    if (!is_email($email)) {
        return;
    }


    // Vor- und Nachname aus den Formularfeldern auslesen und bereinigen
    $vorname  = sanitize_text_field($data['vorname'] ?? '');
    $nachname = sanitize_text_field($data['nachname'] ?? '');
    $fullname = trim($vorname . ' ' . $nachname);

    try {
        $newsletter = Newsletter::instance();

        // Prüfen ob die E-Mail bereits im Newsletter eingetragen ist
        $user = $newsletter->get_user($email);

        if ($user) {
            // Bereits vorhandenen Abonnenten nicht doppelt eintragen
            return;
        } else {
            // Neuen Abonnenten mit Status "C" (confirmed) in Liste 1 eintragen
            $newsletter->save_user([
                'email'  => $email,
                'name'   => $fullname,
                'status' => 'C',
                'list_1' => 1,
            ]);
        }
    } catch (Exception $e) {
        // Fehler
    }
});
