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
                    fontSize: 24,
                    bold: true,
                    alignment: 'center',
                    margin: [0, 5]
                },
                {
                    text: info.subTitle,
                    alignment: 'center',
                    margin: [0, 5]
                },
                {
                    qr: info.url,
                    alignment: 'center',
                    margin: [0, 20]
                },
                {
                    text: info.url,
                    alignment: 'center'
                }
            ]
        };

        pdfMake.createPdf(docDefinition).open();
    }

}
