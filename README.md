# 🛡️ SafeVideo

**Analyseur intelligent de contenu YouTube pour une navigation familiale sécurisée.**

`SafeVideo` est une solution basée sur Laravel conçue pour automatiser l'audit moral et pédagogique des vidéos YouTube. Grâce à l'intelligence artificielle (Gemini 2.0 Flash), le système transforme des métadonnées brutes en décisions claires pour les parents, garantissant un environnement numérique sain.

---

## 🚀 Fonctionnalités Clés

- **Audit Moral Automatisé** : Évaluation du contenu selon des critères de bienveillance et d'éthique.
- **Double Source de Données** : Utilisation croisée de la transcription (RapidAPI) et de la recherche web (Google Search) pour une précision maximale.
- **Classification par Âge** : Segmentation stricte (`3-5`, `6-11`, `12-16`, `17+`) pour une intégration facile en base de données.
- **Système Anti-Doublon** : Vérification intelligente de l'ID YouTube en base de données avant chaque analyse pour optimiser les coûts API.
- **Résilience API** : Gestion native des limites de débit (Error 429) et des filtres de sécurité Google.

---

## 🛠️ Configuration du Projet

### 1. Prérequis

- PHP 8.2+
- Laravel 10 ou 11
- Une clé API **Google Gemini** (via Google AI Studio)
- Une clé **RapidAPI** (abonné au service `youtube-transcript3`)

### 2. Variables d'environnement (`.env`)

Ajoutez les clés suivantes à votre fichier `.env` à la racine du projet :

```env
# Configuration SafeVideo
GEMINI_API_KEY=votre_cle_gemini_ici
RAPIDAPI_KEY=votre_cle_rapidapi_ici
```

Designed and developed by Archippe Muhayiri
