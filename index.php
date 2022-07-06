<!DOCTYPE html>
<html>
<title> Datatables Serverside With PostgreSQL</title>

<head>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.20/css/jquery.dataTables.min.css">
    <script type="text/javascript" language="javascript" src="https://code.jquery.com/jquery-3.3.1.js"></script>
    <script type="text/javascript" language="javascript" src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript" language="javascript">
        $(document).ready(function() {
            var dataTable = $('#dataku').DataTable({
                "processing": true,
                "serverSide": true,
                "ajax": {
                    url: "./server_processing.php", //tentukan dimana file direktori dari  server_prosessing.php
                    type: "post",
                    error: function() {
                        $(".dataku-error").html("");
                        $("#dataku").append('<tbody class="dataku-error"><tr><th colspan="3">Tidak ada data untuk ditampilkan</th></tr></tbody>');
                        $("#dataku-error-proses").css("display", "none");

                    }
                }
            });
        });
    </script>
    <style>
        div.container {
            margin: 0 auto;
            max-width: 760px;
        }

        div.header {
            margin: 100px auto;
            line-height: 30px;
            max-width: 760px;
        }

        body {
            background: #ffff;
            color: #333;
            font: 90%/1.45em "Helvetica Neue", HelveticaNeue, Verdana, Arial, Helvetica, sans-serif;
        }
    </style>
</head>

<body>
    <div class="header">
        <h1> Show data from database Using DataTables Serverside With PostgreSql</h1>
    </div>
    <div class="container">
        <table id="dataku" cellpadding="0" cellspacing="0" border="0" class="display table-sm table-bordered" width="100%">
            <thead>
                <tr>
                    <th>Kode Laporan</th>
                    <th>Nama Laporan</th>
                    <th>Keterangan</th>
                    <th>Status</th>
                </tr>
            </thead>
        </table>
    </div>
    <br>
    <div >
    <footer class="bg-light text-center text-lg-start">
        <!-- Copyright -->
        <div class="text-left p-3" style="background-color: rgba(0, 0, 0, 0.2);">
            © 2022 Copyright:
            <a class="text-dark" href="https://github.com/densuz">Deni Susanto</a>
        </div>
        <!-- Copyright -->
    </footer>
    </div>
</body>

</html>
