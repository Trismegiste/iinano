

(function () {

    var sourceCollection = db[sourceName];
    var targetCollection = db[targetName];
    targetCollection.drop();

    var query = {}
    query[classAliasKey] = {$in: aliases}
    var cursor = sourceCollection.find(query)

    while (cursor.hasNext()) {
        var doc = cursor.next()
        // root
        if (doc.abusive.length !== 0) {
            targetCollection.save({
                type: doc[classAliasKey],
                counter: Object.keys(doc.abusive).length,
                fk: {id: doc._id.str},
                content: {message: doc.message}
            })
        }
        // commentary
        doc.commentary.forEach(function (comment) {
            if (comment.abusive.length !== 0) {
                targetCollection.save({
                    type: comment[classAliasKey],
                    counter: Object.keys(comment.abusive).length,
                    fk: {id: doc._id.str, uuid: comment.uuid},
                    content: {message: comment.message}
                })
            }
        })
    }

})()

