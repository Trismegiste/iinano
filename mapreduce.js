
var mapfunc = function () {
    var pk = this._id
    // root entity
    if (isObject(this.abusive)) {
        for (var key in this.abusive) {
            emit({id: pk, type: 'root'}, 1)
        }
    }
    // commentaries
    this.commentary.forEach(function (comment) {
        if (isObject(comment.abusive)) {
            for (var key in comment.abusive) {
                emit({id: pk, type: 'commentary', uuid: comment.uuid}, 1)
            }
        }
    })
}

var redfunc = function (key, values) {
    return Array.sum(values)
}

db.dokudoki.mapReduce(mapfunc, redfunc, {
    out: 'abusivereport',
    query: {'-class': {'$in': ['small', 'status']}}
})