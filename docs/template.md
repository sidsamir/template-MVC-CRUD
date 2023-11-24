# MVC-CRUD de zéros (form-scratch)

## La config

On installe composer avec la commande (composer intall), il faut au préalable avoir configuré un fichier .json ( on peut très bien installer chaque dépendence avec une commande composer comme par exemple : ```composer require symfony/var-dumper```

```
{
    "require": {
        "symfony/var-dumper": "^5.0",
        "altorouter/altorouter": "^2.0",
        "benoclock/alto-dispatcher": "^1.3"
    },
    "autoload": {
        "psr-4": {"App\\": "app/"}
    }
}
```
Dans mon composer.json, on retrouve **symfony var dumper, alto routeur et alto-dispatcher**.

- Symfony var dumper va me servir à afficher des dump plus compréhensible avec la commande dump() ou des dump qui mettent fin au code avec la commande dd().
- AltoRouter va me servir à gérer mes routes coupler avec alto-dispatcher qui lui va expédier après le routage d'AltoRouter.
- Autoload va me servir pour l'utilisation des namespaces, il va éfféctuer le chargement automatique des classes en utilisant le standard PSR-4. ```"psr-4": {"App\\": "app/"}`` **App** peut-etre changé en ce que l'on souhaite, **app** doit correspondre à notre dossier parent.

Je crée ensuite un fichier .gitignore pour y placer mes dossiers/fichiers sensibles pour qu'ils ne finissent pas sûrs git lors des push. 

## Le MVC

Dans ce template l'architècture logicielle utilisée sera le MVC.
- Je crée un dossier app qui va contenir mes : **Controllers** ,**Models**, **Views** et **Utils**
- Dans ce dossier, j'ajoute aussi les informations de connexion à ma Database, je crée un fichier exemple si le projet doit être clôné.
- Dans le dossier utils, j'ajoute un fichier dataBases, ce fichier va servir à créer et fournir une seule instance de connexion PDO, il est basé sur le design pattern **Singleton**. (Pour plus de détail voir les commentaires du fichier en question).

### Les Controllers

**Le controller va servir de relais entre le modèl et la vue, c'est dans ce dernier que la logique metier va être implantée**

- Le **CoreController**, dans ce controller ont y retrouve : un namespace, la class CoreController en abstract (cette class est en abstract, car on ne veut pas l'instancier directement, on veut uniquement s'en servir en héritage), une propriété $router (pour stocker le routeur et pouvoir faire des $router -> generate dans tous nos contrôleurs), une fonction __construct, comme à chaque page demandée par l'utilisateur on passe par le CoreController et on instancie la fonction __construct, on va mettre notre vérification d'accès à l'intérieur, il y aura aussi notre protection **CSRF** grâce au token, 
il la méhode show permettant d'afficher du code HTML en se basant sur les views
- On peut aussi ajouter le **SecurityController** qui sert au login des utilisateurs.
- On peut ajouter le **UserController** qui gère l'ajout, la modification ou la suppression d'utilisateur (à supprimer si le client n'en veut pas).
- On peut ajouter le **HomeController** qui gère l'affichage de la page d'accueil

### Les Models

**Le Model va servir à interagir avec la BDD (on crée un model par table dans notre BDD)** 
- ici de base il y aura les models qui vont de pair avec nos controllers donc le **CoreModel** et **AppUser** (SecurityController et UserController utilise le meme Model)
**<span style="color : red"> Attention bien remplacer les propriétés, le nom et les propriétés de la table dans nos request sql et les getter et setter**


### Les Views 

**La vue sert à afficher nos données dans du html**

**<span style="color : red">Attention les vues qui possèdent deja une intègration ont des class bootstrap donc à installer ou à changer par du css :)**

- Dans le dossier View, on retrouvera des sous-dossiers, pour le template le dossier layout, main, partials,error et security serons deja présent.
  **<span style="color : red"> Attention les controllers sont implantés tout comme les models mais aucune vue n'est créée et aucune route non plus**
- Dans le dossier main la vue et le template d'ajout d'utilisateur et deja fournis à ajuster ou supprimer si besoin.
- Dans le dossier security la vue et le template de login et deja fournis à ajuster ou supprimer si besoin.

### Dossier public

- Dans le dossier public, on retrouve tout ce qui sera afficher dans le navigateur **<span style="color : red">ne mettre aucun fichier sensible,** 
on va retrouver de base nos assets (js, css, images, fonds ...) on va aussi retrouver notre point d'entrer unique (FrontController) **index.php**

### Le dossier routes

- Dans le dossier route on vien ajouter toutes nos routes et ont inclus ça dans le FrontController voir l'exemple avec la page home. 
