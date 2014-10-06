

(function () {

    db.abusereport.drop();

    var cursor = db.dokudoki.find({'-class': {$in: ['small', 'status']}})

    while (cursor.hasNext()) {
        var doc = cursor.next()
        // root
        if (doc.abusive.length !== 0) {
            db.abusereport.save({
                type: doc['-class'],
                counter: Object.keys(doc.abusive).length,
                fk: {id: doc._id.str},
                content: {message: doc.message}
            })
        }
        // commentary
        doc.commentary.forEach(function (comment) {
            if (comment.abusive.length !== 0) {
                db.abusereport.save({
                    type: 'commentary',
                    counter: Object.keys(comment.abusive).length,
                    fk: {id: doc._id.str, uuid: comment.uuid},
                    content: {message: comment.message}
                })
            }
        })
    }

})()

