;function Major(args) {
    this.role = args.role;
    this.ser = args.ser;
    this.game = args.game;
    this.roleName = args.roleName;
    this.server = args.server;
    this.gameId = args.gameId;
    this.id = args.id;
    this.createMajor = args.createMajor;
    this.userId = args.userId;
    this.alert = args.alert;
    this.rebate = args.rebate;
    this.newAdmin = args.newAdmin;
    this.gameAuth = args.gameAuth;
}

Major.prototype = {
    update: function () {
        var id = $(this.id).val();
        var ser = $(this.ser);
        var role = $(this.role);
        var game = $(this.game);
        $(this.roleName).on('change', function () {
            var roleName = this.value;
            role.on('click', function () {
                Major.prototype.updateAjax({role_name: roleName, id: id});
            });
        });
        $(this.server).on('change', function () {
            var server = this.value;
            ser.on('click', function () {
                Major.prototype.updateAjax({server: server, id: id});
            });
        });
        $(this.gameId).on('change', function () {
            var gameId = this.value;
            game.on('click', function () {
                Major.prototype.updateAjax({game_id: gameId, id: id});
            });
        });
    },
    updateAjax: function (params) {
        if (params) {
            $.getJSON('/major-user/update', params, function (data) {
                $('#Modal').modal('show');
            });
        }
    },
    addRebate: function () {
        var userId = $(this.userId).val();
        $(this.rebate).on('click', function () {
            var keys = $('#payment-list').yiiGridView('getSelectedRows');
            var rebate = $('#rebate-rebate').val();
            var rebated_at = $('#rebate-rebated_at').val();
            var comment = $('#rebate-comment').val();

            $.post('/rebate/create', {
                ids: keys,
                rebate: rebate,
                rebated_at: rebated_at,
                comment: comment,
                user_id: userId
            }, function (data) {
                $('#Modal').modal('hide');
                window.location.reload();
            }, 'json');
        })
    },
    create: function () {
        var createMajor = $(this.createMajor);
        var userId = $(this.userId);
        createMajor.on('click', function () {
            $('#alert').children('div').addClass('hidden');
            $.getJSON('/major-user/create', {user_id: userId.val()}, function (data) {
                if (data['output']) {
                    $($('#alert').children('div').get(0)).removeClass('hidden');
                    $('#Modal').modal('hide');
                } else {
                    $($('#alert').children('div').get(1)).removeClass('hidden');
                    $('#Modal').modal('hide');
                }
            });
        });

    },
    admin: function () {
        var Admin = $(this.newAdmin);
        var id = $('#admin-id');
        Admin.on('click', function () {
            if (id.val() != '') {
                $.getJSON('/major-user/new-admin', {admin_id: id.val()}, function (data) {
                    if (data['output']) {
                        $('#Modal').modal('hide');
                        window.location.reload();
                    }
                });
            }
        })

    },
    auth: function () {
        var Auth = $(this.gameAuth);

        Auth.on('change', function () {
            window.location.href = '/major-user/auth-edit?id=' + Auth.val();
        })
    },
    selectAll: function () {
        $('#selectAll').on('click', function () {
            var checkBoxs = $("[name='AdminGameAuth[game_id][]']");
            for (var i = 0; i < checkBoxs.length; i++) {
                if (checkBoxs[i].type == "checkbox") {
                    $(checkBoxs[i]).attr('checked', 'checked');
                    $(checkBoxs[i]).prop('checked', 'checked');
                }
            }
        })
    },
    uSelectAll: function () {
        $('#uSelectAll').on('click', function () {
            var checkBoxs = $("[name='AdminGameAuth[game_id][]']");
            for (var i = 0; i < checkBoxs.length; i++) {
                if ($(checkBoxs[i]).attr('checked') == 'checked') {
                    if (checkBoxs[i].type == "checkbox") {
                        $(checkBoxs[i]).removeAttr('checked');
                    }
                } else {
                    if (checkBoxs[i].type == "checkbox") {
                        $(checkBoxs[i]).attr('checked', 'checked');
                        $(checkBoxs[i]).prop('checked', 'checked');
                    }
                }
            }
        })
    },
    start: function () {
        this.update();
        // alert('修改成功')
    }
};