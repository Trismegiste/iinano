
var mapfunc = function () {
    for (var key in this.abusive) {
        emit({id: this._id, type: 'root'}, 1)
    }
    for (var idx = 0; idx < this.commentary.length; idx++) {
        var comment = this.commentary[idx]
        for (var key in comment.abusive) {
            emit({id: this._id, type: 'commentary', uuid: comment.uuid}, 1)
        }
    }
}

var redfunc = function (key, values) {
    return Array.sum(values)
}

db.dokudoki.mapReduce(mapfunc, redfunc, {
    out: 'essai',
    query: {'-class': {'$in': ['small', 'status']}, abusive: {$ne: []}}
})