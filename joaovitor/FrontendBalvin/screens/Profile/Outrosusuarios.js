import { StyleSheet, Text, View, StatusBar, ScrollView, Image, ActivityIndicator, TouchableOpacity } from 'react-native'
import React, { useEffect } from 'react'
import { Foundation } from '@expo/vector-icons';
import AsyncStorage from '@react-native-async-storage/async-storage';
import { MaterialIcons } from '@expo/vector-icons';
import { LinearGradient } from 'expo-linear-gradient';

const Outrousuarios = ({ navigation, route }) => {
    const [userdata, setUserdata] = React.useState(null)
    const [issameuser, setIssameuser] = React.useState(false)

    const ismyprofile = (
        otheruser
    ) => {

        AsyncStorage.getItem('user').then((loggeduser) => {
            const loggeduserobj = JSON.parse(loggeduser);
            if (loggeduserobj.user._id == otheruser._id) {
                setIssameuser(true)

            }
            else {
                setIssameuser(false)
            }
        })
    }
    const { user } = route.params
    // console.log(user)
    const loaddata = async () => {
        fetch('http://192.168.0.54:3000/outrosusuarios', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ email: user.email })
        })
            .then(res => res.json())
            .then(data => {
                if (data.message == 'Usuário Encontrado') {
                    setUserdata(data.user)
                    ismyprofile(data.user)
                    CheckFollow(data.user)
                }
                else {
                    alert('Usuário não encontrado')
                    navigation.navigate('Pesquisar')
                    // navigation.navigate('Login')
                }
            })
            .catch(err => {
                // console.log(err)
                alert('Algo deu errado')
                navigation.navigate('Pesquisar')
            })
    }
    useEffect(() => {
        loaddata()
    }, [])

    // console.log('userdata ', userdata)


    const FollowThisUser = async () => {
        
        const loggeduser = await AsyncStorage.getItem('user');
        const loggeduserobj = JSON.parse(loggeduser);
        fetch('http://192.168.0.54:3000/seguirusuario', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                followfrom: loggeduserobj.user.email,
                followto: userdata.email
            })
        }).then(res => res.json())
            .then(data => {
                if (data.message == 'Usuário seguido') {
                    
                    loaddata()
                    setIsfollowing(true)
                }
                else {
                    alert('Algo deu errado')
                }
            })
    }

    const [isfollowing, setIsfollowing] = React.useState(false)
    const CheckFollow = async (otheruser) => {
        AsyncStorage.getItem('user')
            .then(loggeduser => {
                const loggeduserobj = JSON.parse(loggeduser);
                fetch('http://192.168.0.54:3000/verificarseguir', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        followfrom: loggeduserobj.user.email,
                        followto: otheruser.email
                    })
                })
                    .then(res => res.json())
                    .then(data => {
                        if (data.message == 'Usuário na seguinte lista') {
                            setIsfollowing(true)
                        }
                        else if (
                            data.message == 'Usuário não está na lista a seguir'
                        ) {

                            setIsfollowing(false)
                        }
                        else {
                            // loaddata()
                            alert('Algo deu errado')
                        }
                    })
            })

    }



    const UnfollowThisUser = async () => {
        
        const loggeduser = await AsyncStorage.getItem('user');
        const loggeduserobj = JSON.parse(loggeduser);
        fetch('http://192.168.0.54:3000/unfollowuser', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                followfrom: loggeduserobj.user.email,
                followto: userdata.email
            })
        }).then(res => res.json())
            .then(data => {
                if (data.message == 'Usuário deixou de seguir') {
                  
                    loaddata()
                    setIsfollowing(false)
                }
                else {
                    alert('Algo deu errado')
                }
            })
    }
    return (
        <View style={styles.container}>      
            {
                userdata ?
                    <ScrollView>
                        <View style={styles.c1}>

                        <TouchableOpacity onPress={() => navigation.goBack()} style={{marginStart: -330, marginTop: 20}} >
                    <MaterialIcons name="arrow-back-ios" size={27} color="black" />
                        </TouchableOpacity>
                            
                        {
                              userdata.profilepic.length > 0 ?

                              <TouchableOpacity style={{width: 110, height: 110,
                              borderRadius: 75, marginTop: -5, justifyContent: 'center', alignItems: 'center'}}>
                                <LinearGradient colors={['#ec230d', '#f75738', '#be6049']} start={{x: 2, y: 0.15}}
                                style={{width: 110, height: 110,
                                borderRadius: 75, justifyContent: 'center', alignItems: 'center'}}>
                                  <Image style={styles.profilepic} source={{ uri: userdata.profilepic }} />
                                  </LinearGradient>
                                  </TouchableOpacity>
                                  :
                                  <TouchableOpacity style={{width: 110, height: 110,
                                    borderRadius: 75, marginTop: -5, justifyContent: 'center', alignItems: 'center'}}>
                                      <LinearGradient colors={['#ec230d', '#f75738', '#be6049']} start={{x: 2, y: 0.15}}
                                      style={{width: 110, height: 110,
                                      borderRadius: 75, justifyContent: 'center', alignItems: 'center'}}>
                                  <Image style={styles.profilepic} source={require('../../images/nopic.png')} />
                                  </LinearGradient>
                                  </TouchableOpacity>
                          }
                            <Text style={styles.txt}>{userdata.nome}</Text>

                            {
                              userdata.description.length > 0 &&
                              <View style={{width: 270, alignSelf: 'center', marginTop: -10}}>
                              <Text style={styles.description}>{userdata.description}</Text>
                              </View>
                          }


                            <View style={styles.c11}>
                                <View style={styles.c111}>
                                    
                                    <Text style={styles.txt2}>{userdata.followers.length}</Text>
                                    <Text style={styles.txt1}>Seguidores</Text>
                                </View>
                                
                                <View style={styles.c111}>

                                    <Text style={styles.txt2}>{userdata.following.length}</Text>
                                    <Text style={styles.txt1}>Seguindo</Text>

                                </View>
                               
                                <View style={styles.c111}>
                                   
                                    <Text style={styles.txt2}>{userdata.posts.length}</Text>
                                    <Text style={styles.txt1}>Posts</Text>
                                </View>
                                
                            </View>

                            {
                                issameuser ?
                                    <></>
                                    :
                                    <View style={styles.row}>
                                        {
                                            isfollowing ?
                                            <TouchableOpacity style={{width: 140, height: 50, justifyContent: 'center',
                                            alignItems: 'center', borderRadius: 19, margin: 10, marginEnd: -5}} onPress={() => UnfollowThisUser()}>
                                                <LinearGradient colors={['#ec230d', '#f75738', '#be6049']} style={{width: 140, height: 50, justifyContent: 'center',
                                            alignItems: 'center', borderRadius: 19,}} start={{x: 2, y: 0.15}}>
                                            <Text style={{fontSize: 15, fontWeight: 'bold', color: 'white',}}
                                            >Seguindo</Text>
                                            </LinearGradient>
                                            </TouchableOpacity>
                                                :
                                                <TouchableOpacity style={{width: 140, height: 50, justifyContent: 'center',
                                                alignItems: 'center', borderRadius: 19, margin: 10, marginEnd: -5}} onPress={() => FollowThisUser()}>
                                                     <LinearGradient colors={['#ec230d', '#f75738', '#be6049']} style={{width: 140, height: 50, justifyContent: 'center',
                                            alignItems: 'center', borderRadius: 19,}} start={{x: 2, y: 0.15}}>
                                                <Text style={{fontSize: 15, fontWeight: 'bold', color: 'white'}}
                                                >Seguir</Text>
                                                </LinearGradient>
                                                </TouchableOpacity>
                                        }
                                        <TouchableOpacity style={{width: 140, height: 50, justifyContent: 'center',
                                        alignItems: 'center', borderRadius: 19, margin: 10}}  onPress={
                                                ()=>{
                                                    navigation.navigate('Mensagem',{
                                                        fuseremail : userdata.email,
                                                        fuserid : userdata._id,
                                                    })
                                                
                                                }
                                            }>
                                                   <LinearGradient colors={['#ec230d', '#f75738', '#be6049']} style={{width: 140, height: 50, justifyContent: 'center',
                                            alignItems: 'center', borderRadius: 19,}} start={{x: 2, y: 0.15}}>
                                        <Text style={{fontSize: 15, fontWeight: 'bold', color: 'white'}}
                                        >Mensagem</Text>
                                        </LinearGradient>
                                        </TouchableOpacity>

                                            {
                                                isfollowing ?
                                                <TouchableOpacity style={{width: 50, height: 50, justifyContent: 'center',
                                                alignItems: 'center', borderRadius: 19, margin: 10, marginStart: -5}} onPress={
                                                    ()=>{
                                                        navigation.navigate('Agendausuario',{
                                                            fuseremail : userdata.email,
                                                            fuserid : userdata._id,
                                                        })
                                                    
                                                    }
                                                }>
                                                    <LinearGradient colors={['#ec230d', '#f75738', '#be6049']} start={{x: 2, y: 0.15}}
                                                    style={{width: 50, height: 50, justifyContent: 'center',
                                                    alignItems: 'center', borderRadius: 19}}>
                                                    
                                                    <MaterialIcons name="event" size={27} color="white" />
                                                    </LinearGradient>
                                                </TouchableOpacity>
                                                : 
                                                <></>
                                            }

                                    </View>
                
                            }


                            {
                                issameuser ?

                                <TouchableOpacity onPress={() => navigation.navigate('Configuracao')} style={{width: 170, height: 45, alignSelf: 'center', 
                                backgroundColor: '#ec230d', marginTop: 20, borderRadius: 19, alignItems: 'center',justifyContent: 'center',}}>
                                    <Text style={{fontSize: 15, fontWeight: 'bold', color: 'white'}}>Editar</Text>
                                  </TouchableOpacity>

                                :
                                <></>
                            }

                        </View>
                        {
                            isfollowing || issameuser ?
                                <View>
                                    {
                                        userdata.posts.length > 0 ?
                                        <View style={styles.c50}>
                                        <Text style={{
                                             color: 'black',
                                             fontSize: 20,
                                             fontWeight: 'bold',
                                             marginTop: 20
      
                                        }}>Posts</Text>
                                           <View style={{width: 80, height: 2}}>
                                           <LinearGradient colors={['#ec230d', '#f75738', '#be6049']}
                                           style={{width: 80, height: 2}} start={{x: 2, y: 0.15}}></LinearGradient>
                                           </View>
                                                <View style={styles.c13}>
                                                    {
                                                        userdata.posts?.map(
                                                            (item) => {
                                                                return (
                                                                    <TouchableOpacity style={{   width: '100%',}} onPress={
                                                                        ()=>{
                                                                            navigation.navigate('Outroususariosfotos',{
                                                                                fuseremail : userdata.email,
                                                                                fuserid : userdata._id,
                                                                            
                                                                            })
                                                                        
                                                                        }
                                                                    }
                                                            >
                                                                    <Image key={item.post} style={styles.postpic}
                                                                        source={{ uri: item.post }}
                                                                    />
                                                                    </TouchableOpacity>
                                                                )
                                                            }
                                                        )
                                                    }
                                                </View>
                                            </View>
                                            :
                                            <View style={styles.c2}>
                                    <Text style={styles.txt1}>Usuário não tem nenhuma publicação </Text>
                                </View>
                                    }
                                </View>

                                :
                                <View style={styles.c2}>
                                <Text style={styles.txt1}>Seguir para ver as publicações</Text>
                            </View>
                        }
                    </ScrollView>

                    :
                    <ActivityIndicator size="large" color="white" />
            }

        </View>
    )
}

export default Outrousuarios

const styles = StyleSheet.create({
    container: {
        width: '100%',
        height: '100%',
        backgroundColor: '#FAFAFA',
       
    },

    c50: {
        width: '100%',
        alignItems: 'center',
      },

    c1: {
        width: '100%',
    height: 400,
    borderBottomLeftRadius: 30, borderBottomRightRadius: 30,
    alignItems: 'center',
    backgroundColor: 'white', elevation: 10
    },

    profilepic: {
        width: 100,
        height: 100,
        backgroundColor: 'white',
        borderRadius: 75,
        margin: 10,
    },
    txt: {
        color: 'black',
    fontSize: 30,
    fontWeight: 'bold',
    borderRadius: 20,
    height:55,
    marginTop: 10
    },
    txt1: {
        color: 'gray',
        fontSize: 15,
    },
    txt2: {
        color: 'black',
        fontSize: 25,
        fontWeight: 'bold'
    },
    c11: {
        width: '100%',
        flexDirection: 'row',
        justifyContent: 'space-around',
        marginTop: 20
    },
    c111: {
        alignItems: 'center',
    },
    vr1: {
        width: 1,
        height: 50,
        backgroundColor: 'white'
    },
    description: {
        width: 250,
        height: 40,
        color: 'black',
        fontSize: 17,
        width: '100%',
        textAlign: 'center',
    },
    postpic: {
        width: '40%',
        height: 150,
        margin: 5,
        borderRadius: 10,
        marginTop: 20,
        marginHorizontal: 10
    },
    c13: {
        flexDirection: 'row',
        flexWrap: 'wrap',
        marginBottom: 20,
        justifyContent: 'center'
    },
    c2: {
        width: '100%',
        alignItems: 'center',
        justifyContent: 'center',
        height: 200
    },
    refresh: {
        position: 'absolute',
        top: 50,
        right: 5,
        zIndex: 1,
    },
    follow: {
        color: '#ec230d',
        fontSize: 18,
        fontWeight: 'bold',
        margin: 10,
        backgroundColor: 'white',
        paddingVertical: 10,
        paddingHorizontal: 30,
        borderRadius: 20,
        width: 140,
        height: 50
    },
    message: {
        color: '#ec230d',
        fontSize: 17,
        textAlign: 'center',
        fontWeight: 'bold',
        margin: 10,
        backgroundColor: 'white',
        borderRadius: 20,
        width: 140,
        height: 50,
    },
    row: {
        flexDirection: 'row',
        marginTop: 5,
        
    }
})


