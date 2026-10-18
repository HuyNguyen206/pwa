import {deleteOutboxItem, getAllOutboxItems} from "./outbox.js";

export async function processOutbox({dryRun = false} = {}) {
    const items = await getAllOutboxItems();

    if (items.length === 0) {
        console.log('No items in outbox to process.');
        return;
    }

    items.sort((a, b) => a.createdAt.localeCompare(b.createdAt));

    for (const item of items) {
        console.log(`Processing outbox item: ${item.id}`);

        if (dryRun) {
            console.log('Dry run mode: not sending request.');
            continue;
        }

        try {
            const response = await fetch(item.endpoint, {
                method: item.method,
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                },
                body: JSON.stringify(item.payload),
            })

            if (response.ok) {
                console.log(`Successfully sent outbox item: ${item.id}`, item);
                await deleteOutboxItem(item.id);
            } else {
                console.log(`Failed to send outbox item: ${item.id}`, item);
            }

        } catch (e) {
            console.warn(`Error occurred while processing outbox item: ${item.id}`, item, e);
        }

    }

}
