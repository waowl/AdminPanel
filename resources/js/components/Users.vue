<template>
    <div class="container" v-if="$gates.isAdmin()">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Users</h3>
                        <div class="card-tools">
                            <button class="btn btn-success" @click="newModal">
                                <i class="fas fa-user-plus"></i>
                                Add new
                            </button>
                        </div>

                    </div>
                    <!-- /.card-header -->
                    <div class="card-body table-responsive p-0">
                        <table class="table table-hover">
                            <tr>
                                <th>ID</th>
                                <th>User</th>
                                <th>Email</th>
                                <th>Type</th>
                                <th>Registered At</th>
                                <th>Actions</th>
                            </tr>
                            <tr v-for="user in users" :key="user.id">
                                <td>{{user.id}}</td>
                                <td>{{user.name}}</td>
                                <td>{{user.email}}</td>
                                <td>{{user.type}}</td>
                                <td>{{user.created_at | prettyDate }}</td>

                                <td>
                                    <a href="" @click.prevent="deleteUser(user)">
                                        <i class="fas fa-trash red"></i>
                                    </a> /
                                    <a href="" @click.prevent="editModal(user)">
                                        <i class="fas fa-edit blue" ></i>
                                    </a>
                                </td>
                            </tr>

                        </table>
                    </div>
                    <!-- /.card-body -->
                </div>
                <!-- /.card -->
            </div>
        </div><!-- /.row -->
        <!--Modal-->
        <div class="modal fade" id="addNew" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLongTitle">{{modalOptions.title}}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form  @submit.prevent="editMode ? editUser() : addUser()">
                            <div class="form-group">

                            <input v-model="form.name" type="text" name="name"
                                   class="form-control" :class="{ 'is-invalid': form.errors.has('name') }" placeholder="Name">
                            <has-error :form="form" field="name"></has-error>
                        </div>
                        <div class="form-group">
                            <input v-model="form.email" type="email" name="email"
                                   class="form-control" :class="{ 'is-invalid': form.errors.has('email') }" placeholder="Email">
                            <has-error :form="form" field="email"></has-error>
                        </div>
                        <div class="form-group">
                            <input v-model="form.password" type="password" name="password"
                                   class="form-control" :class="{ 'is-invalid': form.errors.has('password') }" placeholder="Password">
                            <has-error :form="form" field="password"></has-error>
                        </div>
                        <div class="form-group">
                            <textarea v-model="form.bio"  name="bio"
                                      class="form-control" :class="{ 'is-invalid': form.errors.has('bio') }" placeholder="Bio"></textarea>
                            <has-error :form="form" field="bio"></has-error>
                        </div>
                        <div class="form-group">
                            <select v-model="form.type"  name="type"
                                    class="form-control" :class="{ 'is-invalid': form.errors.has('type') }">
                                <option value="">Select User Role</option>
                                <option value="admin">Admin</option>
                                <option value="user" >User</option>
                                <option value="author" >Author</option>
                            </select>
                            <has-error :form="form" field="type"></has-error>
                        </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save changes</button>
                    </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        data () {
            return {
               modalOptions: {
                   editMode: false ,
                   title: ''
               },
                users : [],
                uid: null,
                form: new Form({
                    name: '',
                    email: '',
                    password: '',
                    type: '',
                    bio: '',
                    photo: '',

                })
            }
        },
        methods: {
            newModal() {
                this.modalOptions.editMode = false
                this.modalOptions.title = 'Add New User'
                this.openModal()

            },
            editModal(user) {
                this.editMode = true
                this.modalOptions.title = 'Edit User'
                this.openModal()
                this.uid = user.id
                this.form.fill(user)
            },
            openModal() {
                this.form.reset()
                $('#addNew').modal('show')
            },
            sortUsers(users) {
                        return users.sort((a, b) => {
                            return a.id - b.id
                })

            },
            loadUsers() {
              axios.get('api/users')
                  .then(({data})=> {
                    this.users =  this.sortUsers(data.data)
                  })
            },
            addUser () {
                this.form.post('api/users')
                    . then(data=> {
                        this.users.push(data.data)
                        $('#addNew').modal('hide')
                        toast({
                            type: 'success',
                            title: 'Created in successfully'
                        })
                    } )
            },
            editUser() {
                this.form.put(`api/users/${this.uid}`)
                    .then((data) => {
                        this.users = this.users.filter((user) => {
                            return user.id !== this.uid
                        })
                        this.users.push(data.data)
                        this.sortUsers(this.users)
                        $('#addNew').modal('hide')
                        toast({
                            type: 'success',
                            title: 'Updated in successfully'
                        })
                    })
            },
            deleteUser(user) {
                swal({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    type: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                    if (result.value) {
                        const position = this.users.indexOf(user)
                        axios.delete(`api/users/${user.id}`)
                            .then(({data}) => {
                                this.users.splice(position, 1)
                            })
                        swal(
                            'Deleted!',
                            'Your file has been deleted.',
                            'success'
                        )
                    }
                })

            }
        },
        created() {
            if(this.$gates.isAdmin())
                this.loadUsers()

        }
    }
</script>