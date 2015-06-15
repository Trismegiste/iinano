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
                    text: info.title + '\n\n',
                    style: 'header'
                },
                { qr: info.url },
                info.url
            ],
            styles: {
                header: {
                    fontSize: 24,
                    bold: true
                }
            }
        };

        pdfMake.createPdf(docDefinition).open();
    }

}
