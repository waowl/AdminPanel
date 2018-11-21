<template>
    <div class="container">
        <div class="col-md-12">
            <div class="card card-default">
                <div class="card-header">Profile</div>

                <div class="box box-widget widget-user">
                    <!-- Add the bg color to the header using any of the bg-* classes -->
                    <div class="widget-user-header bg-black" style="background: url('/img/user_bg.jpg') center center;">
                        <h3 class="widget-user-username">Elizabeth Pierce</h3>
                        <h5 class="widget-user-desc">Web Designer</h5>
                    </div>
                    <div class="widget-user-image">
                        <img class="img-circle" :src="getProfileAvatar()" alt="User Avatar">
                    </div>
                    <div class="box-footer">
                        <div class="row">
                            <div class="col-sm-4 border-right">
                                <div class="description-block">
                                    <h5 class="description-header">3,200</h5>
                                    <span class="description-text">SALES</span>
                                </div>
                                <!-- /.description-block -->
                            </div>
                            <!-- /.col -->
                            <div class="col-sm-4 border-right">
                                <div class="description-block">
                                    <h5 class="description-header">13,000</h5>
                                    <span class="description-text">FOLLOWERS</span>
                                </div>
                                <!-- /.description-block -->
                            </div>
                            <!-- /.col -->
                            <div class="col-sm-4">
                                <div class="description-block">
                                    <h5 class="description-header">35</h5>
                                    <span class="description-text">PRODUCTS</span>
                                </div>
                                <!-- /.description-block -->
                            </div>
                            <!-- /.col -->
                        </div>
                        <!-- /.row -->
                    </div>
                </div>
            </div>
                <div class="card">
                    <div class=" card-body">
                        <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" id="pills-home-tab" data-toggle="pill" href="#pills-home" role="tab" aria-controls="pills-home" aria-selected="true">Activities</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="pills-profile-tab" data-toggle="pill" href="#pills-profile" role="tab" aria-controls="pills-profile" aria-selected="false">Settings</a>
                            </li>

                        </ul>
                        <div class="tab-content" id="pills-tabContent">
                            <div class="tab-pane fade show active" id="pills-home" role="tabpanel" aria-labelledby="pills-home-tab">
                                hy
                            </div>
                            <div class="tab-pane fade" id="pills-profile" role="tabpanel" aria-labelledby="pills-profile-tab">
                                <form>
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
                                        <input  type="file" name="photo" @change="updatePhoto"
                                               class="" :class="{ 'is-invalid': form.errors.has('photo') }" />
                                        <has-error :form="form" field="photo"></has-error>
                                    </div>
                                    <div class="form-group">
                                <textarea v-model="form.bio"  name="bio"
                                          class="form-control" :class="{ 'is-invalid': form.errors.has('bio') }" placeholder="Bio"></textarea>
                                        <has-error :form="form" field="bio"></has-error>
                                    </div>
                                    <button type="submit" @click.prevent="updateProfile(form.id)" class="btn btn-success">Update</button>
                                </form>
                            </div>
                        </div>

                    </div>
                </div>

    </div>
    </div>

</template>

<script>
    export default{
        data () {
            return  {
                form:  new Form({
                    id: '',
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
            getProfileAvatar() {
              return  (this.form.photo.length < 200 ) ? `/img/profile/${this.form.photo}` : this.form.photo
            },
            updateProfile(id) {
                this.form.put(`api/profile/${id}`)
                    .then(({data}) => {
                        toast({
                            type: data.status,
                            title: 'Updated in successfully'
                        })
                    })
            },
            updatePhoto(e){
                let file = e.target.files[0]
                const reader = new FileReader()
                if (file['size'] < 2111775 ) {
                    reader.onloadend = (file) => {
                        this.form.photo = reader.result
                    }
                    reader.readAsDataURL(file)
                } else {
                    swal(
                        'Error!',
                        'Your file too large',
                        'error'
                    )
                }
            }
        },
        created() {
            axios.get('api/profile')
                .then(({data}) => {
                this.form.fill(data)
            })
        }
    }
</script>
<style>
    .form {
        padding: 20px;
    }
</style>