/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

var coupon = {
    generateFlyer: function (info) {

        var docDefinition = {
            content: [
                {
                    text: info.title,
                    fontSize: 32,
                    bold: true,
                    alignment: 'center',
                    margin: [0, 10]
                },
                {
                    text: info.subTitle,
                    alignment: 'center',
                    margin: [0, 10],
                    fontSize: 24
                },
                {
                    qr: info.url,
                    fit: 400,
                    alignment: 'center',
                    margin: [0, 40]
                },
                {
                    text: info.url,
                    alignment: 'center',
                    fontSize: 14
                }
            ]
        };

        pdfMake.createPdf(docDefinition).open();
    }

}
