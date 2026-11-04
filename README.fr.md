# NODERA KASIR POS

<p align="center">
  <img src="assets/logo.png" alt="NODERA KASIR POS" width="180"/>
</p>

<p align="center">
  <a href="https://panel.dgtlnetsolution.com"><img src="https://img.shields.io/badge/Plateforme-Android%20%7C%20Windows%2010%2B%20%7C%20Linux-0052CC.svg?style=for-the-badge&logo=android" alt="Plateforme"></a>
  <a href="https://panel.dgtlnetsolution.com"><img src="https://img.shields.io/badge/Moteur-PHP%208.2%20Micro--Serveur%20Embarqu%C3%A9-777BB4.svg?style=for-the-badge&logo=php" alt="Moteur"></a>
  <a href="https://panel.dgtlnetsolution.com"><img src="https://img.shields.io/badge/Base%20de%20donn%C3%A9es-SQLite%203%20Z%C3%A9ro--Config-003B57.svg?style=for-the-badge&logo=sqlite" alt="Base de données"></a>
  <a href="https://panel.dgtlnetsolution.com"><img src="https://img.shields.io/badge/Version-v1.0.5%20Officielle-00B074.svg?style=for-the-badge" alt="Version"></a>
  <a href="https://panel.dgtlnetsolution.com"><img src="https://img.shields.io/badge/S%C3%A9curit%C3%A9-Obfusqu%C3%A9%20%2B%20Cloud%20HWID-E02424.svg?style=for-the-badge" alt="Sécurité"></a>
</p>

<p align="center">
  <a href="https://github.com/fitratan/NODERA-KASIR"><img src="https://komarev.com/ghpvc/?username=fitratan-nodera-kasir&label=Vues%20du%20D%C3%A9p%C3%B4t&color=0052cc&style=for-the-badge" alt="Vues du Dépôt"></a>
  <a href="https://github.com/fitratan/NODERA-KASIR/releases"><img src="https://img.shields.io/badge/APK%20Disponible-v1.0.5%20Officielle-38BDF8.svg?style=for-the-badge&logo=android" alt="APK Disponible"></a>
  <a href="https://panel.dgtlnetsolution.com"><img src="https://img.shields.io/badge/Support-Caisse%20Multi--Commerces-8B5CF6.svg?style=for-the-badge" alt="Multi-Commerces"></a>
</p>

<p align="center">
  <a href="README.md"><b>🇮🇩 Bahasa Indonesia</b></a> •
  <a href="README.en.md"><b>🇬🇧 English</b></a> •
  <a href="README.fr.md"><b>🇫🇷 Français</b></a>
</p>

---

## 📌 À Propos de NODERA KASIR POS

**NODERA KASIR POS** est un système de caisse enregistreuse (*Point of Sale*) et d'intelligence commerciale multi-activités de nouvelle génération, développé par l'équipe **NODERA**. Conçu selon une philosophie stricte **100% Offline-First**, le système fonctionne de manière totalement autonome sans aucune dépendance à Internet ni à un serveur externe, tout en intégrant une synchronisation cloud pour les licences et une connectivité réseau local (LAN/WiFi).

L'application s'adapte parfaitement à divers secteurs commerciaux :
- **Lavage & Entretien de Véhicules** : Lavage moto, lavage auto, tapis & casques avec gestion des tickets d'attente.
- **Salons de Coiffure & Barbiers** : Forfaits de coupe, soins et calcul automatique des commissions des coiffeurs.
- **Blanchisserie & Ateliers de Réparation** : Suivi des tickets de dépôt, état d'avancement et alertes de retrait.
- **Cafés, Snacks & Restaurants** : Commande rapide au comptoir, impression des bons cuisine (kitchen bar) et gestion des notes ouvertes (open bill).
- **Épiceries & Commerce de Détail** : Vente par lecture de codes-barres (caméra/laser), tarification de gros par paliers et gestion des stocks en temps réel.

---

## 📊 Tableau Comparatif : Caisses Classiques vs NODERA KASIR POS

| Paramètre & Fonctionnalité | Applications de Caisse Ordinaires | NODERA KASIR POS (Standalone Android & Desktop) |
| :--- | :--- | :--- |
| **Architecture Android** | Simple vue web (webview) nécessitant un PC ou un serveur actif en continu | **APK 100% Autonome** : Intègre un *Micro-Serveur PHP 8.2 Embarqué* directement sur l'appareil Android sans aucun PC requis |
| **Besoins en Serveur PC** | Nécessite l'installation de XAMPP/WAMP ou la location d'un serveur VPS mensuel | **Zéro Configuration Serveur** : Démarre instantanément sur Android (via APK) ou Windows (via le binaire natif `NoderaKasir.exe`) |
| **Dépendance à Internet** | Connexion Internet obligatoire en permanence ; encaissement bloqué lors des pannes | **100% Offline-First** : Base de données locale SQLite 3 haute vitesse (mode WAL) ; encaissement fluide sans forfait Internet |
| **Barre d'État Système** | Mode kiosque plein écran forcé masquant l'état de la batterie et le signal | **Barre d'État Native Toujours Visible** : L'heure, la batterie et le signal réseau restent parfaitement visibles en portrait et paysage |
| **Écran de Démarrage (Splash)** | Écran blanc clignotant désagréable (*white-flash flicker*) | **Écran Branded & Animé** : Fond sombre `#08101E`, logo pulsant, barre de progression bleu ciel (`#38BDF8`) et transition fluide de 350 ms |
| **Adaptabilité d'Affichage** | Disposition figée ; polices réduites ou coupées lors de la rotation de l'écran | **Double Disposition Adaptative** : Bascule automatique en 2 colonnes (70% catalogue produits \| 30% panier actif) sur tablette et écran paysage |
| **Module Services & Véhicules** | Limité aux articles physiques ; impossible d'enregistrer les plaques minéralogiques | **Module Lavage & Services Complet** : Saisie de la plaque d'immatriculation, impression du ticket de file (`A-01`) et répartition des commissions |
| **Impression Thermique** | Multiples fenêtres contextuelles de confirmation du navigateur | **Direct RawBT Android & ESC/POS Natif** : Impression thermique Bluetooth rapide (58mm/80mm) en un clic sans boîte de dialogue |
| **Sécurité & Intégrité** | Code source vulnérable, mots de passe en clair, base facile à corrompre | **Moteur Obfusqué + Anti-Altération** : Chiffrement multi-couches XOR + Deflate, hachage BCrypt, garde anti-recul d'horloge et licence HWID |

---

## 🚀 Matrice Fonctionnelle Complète

### 1. Architecture & Performance Système
| Fonctionnalité | Spécifications & Détails Techniques |
| :--- | :--- |
| **Moteur d'Exécution** | PHP 8.2 embarqué (JIT activé, binaire POSIX NDK compilé pour ARM64, ARMv7, x86_64) |
| **Moteur de Base de Données** | SQLite 3 sans maintenance (Fichier unique sécurisé, mode *WAL* pour haute concurrence) |
| **Modes de Déploiement** | **Mode 1** : Micro-serveur interne autonome sur smartphone/tablette Android.<br>**Mode 2** : Serveur réseau LAN sur PC de bureau pour caisses multiples. |
| **Démarrage à Froid** | Démarrage optimisé sans scintillement avec le thème sombre natif Android `#08101E`. |

### 2. Interface Utilisateur & Ergonomie (UI/UX)
| Fonctionnalité | Spécifications & Détails Techniques |
| :--- | :--- |
| **Orientation Intelligente** | S'adapte automatiquement aux smartphones (Portrait pour saisie à une main) et tablettes (Paysage 2 colonnes interactif). |
| **Visibilité de la Barre d'État** | Maintient la barre d'état système affichée pour permettre au personnel de surveiller le niveau de batterie et la connexion Bluetooth. |
| **Animations & Transitions** | Pulsation douce du logo, indicateur de chargement horizontal bleu ciel et transition de fondu de 350 ms. |
| **Localisation Multilingue Complète** | **5 Langues avec Parité Absolue (2 373 clés)** : **Français (FR)**, **Anglais (EN)**, **Indonésien (ID)**, **Espagnol (ES)** et **Tagalog / Philippin (TL)**. |

### 3. Encaissement, Services & Lavage de Véhicules
| Fonctionnalité | Spécifications & Détails Techniques |
| :--- | :--- |
| **Caisse Vente Rapide** | Recherche instantanée d'articles/services, gros boutons de catégories, lecture code-barres par caméra et saisie manuelle. |
| **Gestion des Plaques d'Immatriculation** | Idéal pour lavage et garage : enregistrement direct du numéro minéralogique du client dans le panier de caisse. |
| **Tickets de File d'Attente** | Émission automatique de numéros d'attente (ex. : `A-01`, `B-12`) à apposer sur le rétroviseur ou le tableau de bord. |
| **Calcul des Commissions Staf** | Allocation automatique de commissions au pourcentage ou au forfait pour chaque technicien/laveur par transaction. |
| **Prix de Gros par Paliers** | Modèles de tarification dégressive selon la quantité achetée (ex. : >= 3 articles = remise automatique). |
| **Notes Ouvertes / Commandes en Attente** | Mise en attente temporaire de la commande avec reprise du paiement dès la fin du repas ou de la prestation. |

### 4. Paiements & Impression Immédiate de Reçus
| Fonctionnalité | Spécifications & Détails Techniques |
| :--- | :--- |
| **Paiement Espèces** | Boutons de montants rapides (`Montant Exact`, `20.000`, `50.000`, `100.000`, etc.) et calcul instantané de la monnaie à rendre. |
| **Paiements Électroniques** | QRIS Dynamique (Nodera Pay Gateway), Virement Bancaire, Carte Débit et Compte Client / Ardoise (crédit). |
| **Imprimantes Thermiques (58mm & 80mm)** | Formatage ESC/POS haute précision pour papier thermique 58mm (32 caractères) et 80mm (48 caractères). |
| **Impression Directe RawBT Android** | Impression Bluetooth instantanée via intent natif `android.intent.action.SEND` sans fenêtre de confirmation. |
| **En-tête & Pied de Reçu Personnalisés** | Personnalisation facile du nom de l'établissement, adresse, numéro WhatsApp, numéro de ticket et mentions de bas de page. |

### 5. Sécurité, Anti-Altération & Protection de Licence
| Fonctionnalité | Spécifications & Détails Techniques |
| :--- | :--- |
| **Code de Production Obfusqué** | Code source compilé et obfusqué (XOR dynamique + Deflate) avant intégration dans l'APK ou le binaire desktop. |
| **Hachage Sécurisé BCrypt** | Tous les codes PIN caissier et mots de passe d'administration sont protégés par l'algorithme robuste `bcrypt`. |
| **Protection Anti-Recul d'Horloge** | Contrôle de la cohérence de l'horloge système pour empêcher les tentatives d'extension de la période d'essai par recul de date. |
| **Verrouillage par Matériel (HWID)** | Association cryptographique à l'identifiant matériel unique de l'appareil (`NDR-POS-...`) validée via le Cloud NODERA. |

---

## 💻 Guide d'Installation & Utilisation

| Plateforme | Méthode d'Installation | Fichier / Paquet Exécutable | Étapes d'Utilisation |
| :--- | :--- | :--- | :--- |
| **Tablette / Smartphone Android** | Installation APK Officiel | `dist/Nodera-Kasir-POS.apk` | 1. Téléchargez et installez l'APK sur votre appareil.<br>2. Lancez l'application ; le serveur local démarre automatiquement.<br>3. Commencez immédiatement vos encaissements hors ligne. |
| **PC de Bureau Windows (PC Caisse)** | Lanceur Windows Natif | `dist/NoderaKasir.exe` | 1. Extrayez l'archive dans un dossier local.<br>2. Exécutez `NoderaKasir.exe` (actif dans la barre des tâches).<br>3. Accédez à la caisse via WebView2 interne ou navigateur. |
| **Postes Clients Réseau Local (WiFi/LAN)** | Navigateur Web Standard | Navigateur Moderne (Chrome/Edge/Safari) | 1. Connectez les smartphones/tablettes au même réseau WiFi que le PC hôte.<br>2. Ouvrez l'URL : `http://<IP-DU-PC-HOTE>:8088`. |

---

## 🔑 Identifiants de Connexion par Défaut

| Espace Utilisateur | URL d'Accès | Nom d'Utilisateur | Mot de Passe / Code PIN par Défaut | Niveau de Droits |
| :--- | :--- | :--- | :--- | :--- |
| **Back-Office Administration** | `http://localhost:8088/admin` | `admin` (ou `admin@nodera.id`) | Mot de passe : `admin`<br>PIN : `888800` | Accès complet aux rapports, stocks, employés, paramètres et licences |
| **Caisse Enregistreuse POS** | `http://localhost:8088/` | Caissier Principal | PIN : `123456` | Ventes, saisie des files d'attente, impression de tickets et clôture de shift |
| **Superviseur / Gérant** | `http://localhost:8088/` | Propriétaire / Superviseur | PIN : `888800` | Annulation de vente (void), suppression de note en attente et remboursements |

> ⚠️ **Avertissement de Sécurité** : Pour la sécurité de vos données commerciales, modifiez immédiatement les mots de passe et codes PIN par défaut dans le menu **Paramètres des Utilisateurs** dès la première installation.

---

## 🌐 Ports Réseau & Points d'Accès

| Composant Réseau | Port / Chemin URL | Protocole | Rôle & Description |
| :--- | :--- | :--- | :--- |
| **Serveur Web Local** | `8088` | HTTP | Port standard du micro-serveur interne PHP 8.2 |
| **Interface Caisse POS** | `http://127.0.0.1:8088/` | HTTP GET/POST | Écran principal d'encaissement et de gestion des files |
| **Panneau Back-Office** | `http://127.0.0.1:8088/admin` | HTTP GET/POST | Gestion des catalogues, stocks, tarifications et rapports |
| **Moteur API REST** | `http://127.0.0.1:8088/api.php` | JSON REST API | Points d'accès pour les ventes, l'impression et la synchronisation |
| **API Licence Cloud** | `https://panel.dgtlnetsolution.com` | HTTPS | Validation de licence commerciale et activation de l'identifiant matériel |

---

## 📱 Configuration Système Requise

| Composant Matériel | Exigences Minimales Android | Exigences Minimales Windows PC |
| :--- | :--- | :--- |
| **Système d'Exploitation** | Android 7.0 (Nougat) jusqu'à Android 14+ | Windows 10 / Windows 11 (64-Bit) |
| **Architecture Processeur** | ARM64 (AArch64), ARMv7 (32-Bit), ou x86_64 | Dual Core 1.8 GHz+ (Intel Core i3 / équivalent AMD) |
| **Mémoire Vive (RAM)** | 2 Go minimum (3 Go+ recommandés pour inventaires volumineux) | 2 Go de RAM minimum (4 Go+ recommandés) |
| **Espace de Stockage** | 150 Mo d'espace libre minimum | 250 Mo d'espace libre minimum |
| **Imprimante Prise en Charge** | Thermique Bluetooth 58mm / 80mm via RawBT ou WebBT | Thermique USB / Bluetooth / Imprimante Virtuelle POS |

---

## 🛠️ Pipeline de Compilation & d'Obfuscation (Développeurs)

| Étape | Commande CLI | Description Technique |
| :--- | :--- | :--- |
| **1. Obfusquer le Code Source** | `php scripts/build_obfuscator.php` | Nettoie les commentaires, minifie la logique PHP, applique le chiffrement et distribue dans `public/` et `app/` |
| **2. Empaqueter l'Environnement** | `cd app && zip -qr ../android/app/src/main/assets/kasir.zip . && cd ..` | Archive le runtime web obfusqué dans le bundle interne de l'application Android |
| **3. Compiler l'APK Release** | `export JAVA_HOME=/path/to/jdk17`<br>`export PATH=$JAVA_HOME/bin:$PATH`<br>`cd android && ./gradlew assembleRelease` | Compile l'APK de production signé avec Gradle et JDK 17 |
| **4. Copier les Binaires Finaux** | `cp android/app/build/outputs/apk/release/app-release.apk dist/Nodera-Kasir-POS.apk` | Déplace le binaire signé final dans le répertoire public `dist/` |

---

## 📁 Structure du Répertoire du Projet

| Dossier / Fichier | Type | Description |
| :--- | :--- | :--- |
| `android/` | Dossier | Projet Android Studio / Gradle natif (Kotlin, gestionnaire de micro-serveur PHP, bridge) |
| `kasir-src/` | Dossier | Code source maître non chiffré (Environnement de travail et développement) |
| `app/` | Dossier | Runtime applicatif distribué obfusqué (Logique cœur PHP et fichiers de langues) |
| `assets/` | Dossier | Éléments graphiques officiels, logos vectoriels et identité visuelle |
| `bin/` | Dossier | Binaires du micro-serveur autonome PHP 8.2 portable pour Windows |
| `config/` | Dossier | Configuration système, licences locales et protection de l'identifiant matériel |
| `dist/` | Dossier | Fichiers exécutables signés prêts à être installés (`Nodera-Kasir-POS.apk`, `NoderaKasir.exe`) |
| `launcher-go/` | Dossier | Code source du lanceur natif Windows écrit en Go avec intégration WebView2 |
| `scripts/` | Dossier | Scripts d'automatisation, pipeline d'obfuscation et empaquetage |
| `README.md` | Document | Documentation principale (avec navigation interactive en indonésien, anglais et français) |
| `README.en.md` | Document | Documentation complète en langue anglaise |
| `README.fr.md` | Document | Documentation complète en langue française |

---

## 📞 Acquisition de Licence & Support Technique

Les licences commerciales officielles pour **NODERA KASIR POS** peuvent être activées et gérées de manière centralisée :

| Service | Contact & Liens Utiles |
| :--- | :--- |
| **Portail Officiel des Licences** | [panel.dgtlnetsolution.com](https://panel.dgtlnetsolution.com) |
| **Assistance Technique Telegram** | [@nullneko_exe](https://t.me/nullneko_exe) |
| **Service Client WhatsApp** | [+62 851-5517-3547](https://wa.me/6285155173547) |
| **Équipe de Développement** | **Écosystème NODERA (Digital Network Solution)** |

---

<p align="center">
  <b>NODERA KASIR POS</b><br>
  Système de Caisse Enregistreuse Intelligente & Gestion Commerciale<br>
  <i>Copyright &copy; 2026 NODERA Ecosystem. Tous droits réservés.</i>
</p>
