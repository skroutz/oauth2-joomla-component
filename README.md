Joomla v.1.5, v.1.6 and v.2.5 Skroutz Easy component
====================================================

## English

### Requirements

 - [Joomla! v.1.5, v.1.6 or v.2.5](http://www.joomla.org)
 - [VirtueMart 1.1.9 or 2.0.6](http://virtuemart.net)

> **NOTE**:

> The Joomla Development Team **no longer provides support for Joomla 1.5** since April 2012.
Also the VirtueMart Team, with the end of Joomla 1.5, has withdrawn support from VirtueMart 1.1 (last version was 1.1.9).

> Although this component works with Joomla 1.5 and VirtueMart 1.1.9 we urge you to upgrade / migrate your installation
to Joomla 2.5 and VirtueMart 2.0 in order to experience performance benefits, gain from the use of new features,
while protecting your site from potential (undiscovered) security vulnerabilities.

> While this component works with VirtueMart 1.1.9, support for this version of VirtueMart is limited.

> You can find more info about [VirtueMart support on the VirtueMart forum]
(http://forum.virtuemart.net/index.php?topic=109098.0).

### Installation instructions for VirtueMart 2

1. Download the latest version of the component from [Releases][releases]
2. Install Joomla! and VirtueMart
3. Login to the 'Administration' and install the 'Component'
    - Select 'Install / Uninstall' from the 'Extensions' menu
    - Click 'Choose file' and select the extension (`com_skroutzeasy.zip`) from your HDD
    - Upload the file using 'Upload File & Install'

![Installing OAuth2 Joomla Component][oauth2-joomla-component-install]

4. Configure the 'Component'
    - Select the 'Skroutz Easy' component from the 'Component' menu
    - Click the 'Parameters' from the toolbar
    - Add the `client_id` in the 'Client ID' field
    - Add the `client_secret` in the 'Client Secret' field
    - Add the `redirect_uri` (callback) in the 'Redirect URI' field (optional)
      This field can be left empty. It will be calculated automatically.

![Configuring OAuth2 Joomla Component][oauth2-joomla-component-configure]

5. Add extra fields and update mappings (optional)
    - Some fields like (IRS office, Taxpayer Identification Number, etc.) are
      not installed by default in a VirtueMart installation.
    - If you have to provide invoices you need to manually add these fields.
    - Select the 'VirtueMart' component from the 'Component' menu
    - Choose 'Configuration' and then 'Shopper Fields' from the left side menu
    - Press 'New' to add a new field
    - Provide the 'Field name'
      This name should much with the ones preconfigured in the component.
    - Provide the 'Field title' that will be visible to visitors
    - Position the fields accordingly for you registration forms
    - Make sure the fields are published

![Adding new fields for OAuth2 Joomla Component][oauth2-joomla-component-newfield]

![Editing fields of OAuth2 Joomla Component][oauth2-joomla-component-newfield-edit]

6. Further customization / development (optional)
    - In case your Joomla installation is highly customized or you
      have renamed the default columns you might need to edit
      `components/com_skroutzeasy/controller.php` in order to fix the mappings.
    - Edit the mapUserData() function and change the mappings
      between between the JSON and the `data` array.
    - No other changes should be necessary but feel freely to modify
      the code to suit your needs. Please report any bugs you find.

## Greek

### Απαιτήσεις

 - [Joomla! v.1.5, v.1.6 or v.2.5](http://www.joomla.org)
 - [VirtueMart 1.1.9 or 2.0.6](http://virtuemart.net)

> **ΣΗΜΕΙΩΣΗ**:

> Η Ομάδα Ανάπτυξης του Joomla **σταμάτησε να υποστηρίζει το Joomla 1.5** από τον Απρίλιο 2012.
Επίσης η Ομάδα του VirtueMart, με το τέλος του Joomla 1.5, έχει αποσύρει την υποστήριξή της για το VirtueMart 1.1
(η τελευταία έκδοση ήταν η 1.1.9).

> Παρόλο που αυτό το component λειτουργεί με το Joomla 1.5 και το VirtueMart 1.1.9 σας προτρέπουμε να αναβαθμίσετε /
μεταφέρετε την εγκατάστασή σας σε Joomla 2.5 και VirtueMart 2.0 για να επωφεληθείτε από την καλύτερη απόδοση, να
αξιοποιήσετε τα νέα χαρακτηριστικά, και παράλληλα να προστατεύσετε το site σας από πιθανά (άγνωστα) προβλήματα ασφαλείας.

> Μολονότι αυτό το component λειτουργεί με το VirtueMart 1.1.9, η υποστήριξή για τη συγκεκριμένη έκδοση του VirtueMart είναι περιορισμένη.

> Μπορείτε να βρείτε περισσότερες πληροφορίες για την [υποστήριξη του VirtueMart στo forum του VirtueMart]
(http://forum.virtuemart.net/index.php?topic=109098.0).

### Οδηγίες εγκατάστασης για VirtueMart 2

1. Κατεβάστε την τελευταία έκδοση του component από τα [Releases][releases]
2. Εγκαταστήστε το Joomla! και το VirtueMart
3. Συνδεθείτε στο 'Administration' περιβάλλον και εγκαταστήστε το 'Component'
    - Επιλέξτε 'Install / Uninstall' από το μενού 'Extensions'
    - Επιλέξτε 'Choose file' και μετά διαλέξτε το extension (`com_skroutzeasy.zip`) από τον σκληρό δίσκο
    - Εγκαταστήστε το αρχείο επιλέγοντας 'Upload File & Install'

![Installing OAuth2 Joomla Component][oauth2-joomla-component-install]

4. Παραμετροποιήστε το 'Component'
    - Επιλέξτε το 'Skroutz Easy' component από το μενού 'Component'
    - Διαλέξτε τα 'Parameters' από τη μπάρα εργαλείων
    - Προσθέστε το `client_id` στο πεδίο 'Client ID'
    - Προσθέστε το `client_secret` στο πεδίο 'Client Secret'
    - Προσθέστε το `redirect_uri` (callback) στο πεδίο 'Redirect URI' (προεραιτικό)
      Αυτό το πεδίο μπορεί να παραμείνει και κενό. Υπολογίζεται αυτόματα.

![Configuring OAuth2 Joomla Component][oauth2-joomla-component-configure]

5. Προσθέστε extra πεδία και ενημερώστε τις αντιστοιχίες (προεραιτικό)
    - Μερικά πεδία όπως (ΔΟΥ, ΑΦΜ, κτλ.) δεν υπάρχουν στην αρχική εγκατάσταση του VirtueMart.
    - Αν θέλετε να μπορείτε να δώσετε παραστατικά θα πρέπει να προσθέσετε αυτά τα πεδία.
    - Επιλέξτε το 'VirtueMart' component από το μενού 'Component'
    - Επιλέγτε 'Configuration' και έπειτα 'Shopper Fields' από το μενού στα αριστερά
    - Πατήστε 'New' για να βάλετε ένα νέο πεδίο
    - Πληκτρολογήστε το όνομα του πεδίου στο 'Field name'
      Αυτό το όνομα πρέπει να μοιάζει με αυτά που είναι ρυθμισμένα στο component.
    - Πληκτρολογήστε τον τίτλο του πεδίου στο 'Field title' που θα είναι ορατός από τους χρήστες
    - Τοποθετήστε τα πεδία κατάλληλα στη φόρμα εγγραφής
    - Βεβαιωθείτε ότι τα πεδία είναι δημοσιευμένα (published)

![Adding new fields for OAuth2 Joomla Component][oauth2-joomla-component-newfield]

![Editing fields of OAuth2 Joomla Component][oauth2-joomla-component-newfield-edit]

6. Περαιτέρω ρυθμίσεις / ανάπτυξη (προεραιτικό)
    - Στην περίπτωση που η εγκατάσταση του Joomla είναι εξαιρετικά προσαρμοσμένη
      ή έχετε αλλάξει τα προεπιλεγμένα ονόματα στα πεδία ίσως χρειαστεί να αλλάξετε
      το αρχείο `components/com_skroutzeasy/controller.php` για να διορθώσετε τις
      αντιστοιχίες.
    - Αλλάξτε το function mapUserData() για να διορθώσετε τις αντιστοιχίες μεταξύ
      του JSON και του πίνακα δεδομένων (`data` array).
    - Δεν θα πρέπει να χρειάζονται άλλες αλλαγές στον κώδικα αλλά είστε ελεύθεροι
      να προσαρμόσετε τον κώδικα στα μέτρα και στις ανάγκες σας. Παρακαλούμε να
      μας αναφέρεται οτιδήποτε σφάλματα ανακαλύψετε.

[oauth2-joomla-component-install]: https://raw.github.com/skroutz/oauth2-joomla-component/master/doc/oauth2-joomla-component-newfield-edit.png "Installing OAuth2 Joomla component"
[oauth2-joomla-component-configure]: https://raw.github.com/skroutz/oauth2-joomla-component/master/doc/oauth2-joomla-component-configure.png "Configuring OAuth2 Joomla component"
[oauth2-joomla-component-newfield]: https://raw.github.com/skroutz/oauth2-joomla-component/master/doc/oauth2-joomla-component-newfield.png "Adding new field for OAuth2 Joomla component"
[oauth2-joomla-component-newfield-edit]: https://raw.github.com/skroutz/oauth2-joomla-component/master/doc/oauth2-joomla-component-newfield-edit.png "Editing fields of OAuth2 Joomla component"
[releases]: https://github.com/skroutz/oauth2-joomla-component/releases
