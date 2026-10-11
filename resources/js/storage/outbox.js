const DB_NAME = 'field_logger';
const DB_VERSION = 1
const OUTBOX_STORE = 'outbox';

function openDB() {
    return new Promise((resolve, reject) => {
        const request = indexedDB.open(DB_NAME, DB_VERSION);

        request.onupgradeneeded = (event) => {
            const db = event.target.result;

            if (!db.objectStoreNames.contains(OUTBOX_STORE)) {
                const store = db.createObjectStore(OUTBOX_STORE, {keyPath: 'id'});
                store.createIndex('createdAt', 'createdAt', {unique: false});
            }
        };

        request.onsuccess = () => {
            resolve(request.result);
        };
        request.onerror = () => {
            reject(request.error);
        };
    });
}

export async function saveToOutbox(item) {
    const db = await openDB();

    return new Promise((resolve, reject) => {
        const tx = db.transaction(OUTBOX_STORE, 'readwrite');
        const store = tx.objectStore(OUTBOX_STORE);
        const request = store.put(item);

        request.onsuccess = () => {
            resolve();
        };
        request.onerror = (event) => {
            reject(request.error);
        };

    })

}
