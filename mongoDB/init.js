// === Créer la base ===
use gestion_entreprise_S5;

// === Collections ===
db.createCollection("users");
db.createCollection("conversations");
db.createCollection("messages");

// === Ajouter quelques utilisateurs ===
db.users.insertMany([
  { nom: "Rakoto", email: "rakoto@mail.com", date_inscription: new Date() },
  { nom: "Shin", email: "shin@mail.com", date_inscription: new Date() }
]);

// === Ajouter une conversation ===
const convId = db.conversations.insertOne({
  type: "prive",
  participants: [
    { id_employe: 1 },
    { id_employe: 2 }
  ],
  date_creation: new Date()
}).insertedId;

// === Ajouter un message ===
db.messages.insertOne({
  conversation_id: convId,
  id_employe: 1,
  contenu: "Salut !",
  date_envoi: new Date(),
  est_lu: false
});
