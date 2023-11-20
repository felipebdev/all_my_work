import { StyleSheet, Text, View, StatusBar, ScrollView, Image,  ActivityIndicator, TouchableOpacity } from 'react-native'
import React, { useEffect } from 'react'
import AsyncStorage from '@react-native-async-storage/async-storage';
import { LinearGradient } from 'expo-linear-gradient';

const Perfil = ({ navigation }) => {
  const [userdata, setUserdata] = React.useState(null)

  const loaddata = async () => {
      AsyncStorage.getItem('user')
          .then(async (value) => {
              fetch('http://192.168.0.54:3000/userdata', {
                  method: 'POST',
                  headers: {
                      'Content-Type': 'application/json',
                      'Authorization': 'Bearer ' + JSON.parse(value).token
                  },
                  body: JSON.stringify({ email: JSON.parse(value).user.email })
              })
                  .then(res => res.json()).then(data => {
                      if (data.message == 'Usuário Encontrado') {
                          setUserdata(data.user)
                      }
                      else {
                          alert('Login Again')
                          navigation.navigate('Login')
                      }
                  })
                  .catch(err => {
                      navigation.navigate('Login')
                  })
          })
          .catch(err => {
              navigation.navigate('Login')
          })
  }
  useEffect(() => {
      loaddata()
  }, [])

  return (
      <View style={styles.container}>
          <StatusBar />
        
          {
              userdata ?
                  <ScrollView>
                      <View style={styles.c1}>
                        <TouchableOpacity style={{width: 25, height: 27, marginTop: 40, alignSelf: 'flex-end', marginEnd: 40}}
                        onPress={() => navigation.navigate('Solicitacaoagendamento')}>
                            <Image source={require('./../images/calendar.png')} style={{width: 25, height: 25}}/>
                        </TouchableOpacity>
                       
                          {
                              userdata.profilepic.length > 0 ?
                              <TouchableOpacity style={{width: 110, height: 110, backgroundColor: '#ec230d',
                              borderRadius: 75, marginTop: -30, justifyContent: 'center', alignItems: 'center'}}>
                                <LinearGradient colors={['#ec230d', '#f75738', '#be6049']} style={{width: 110, height: 110, backgroundColor: '#ec230d',
                              borderRadius: 75, justifyContent: 'center', alignItems: 'center'}} start={{x: 2, y: 0.15}}>
                                  <Image style={styles.profilepic} source={{ uri: userdata.profilepic }} />
                                  </LinearGradient>
                                  </TouchableOpacity>
                                  :
                                  <TouchableOpacity style={{width: 110, height: 110, backgroundColor: '#ec230d',
                                  borderRadius: 75, marginTop: -30, justifyContent: 'center', alignItems: 'center'}}>
                                    <LinearGradient colors={['#ec230d', '#f75738', '#be6049']} style={{width: 110, height: 110, backgroundColor: '#ec230d',
                                  borderRadius: 75, justifyContent: 'center', alignItems: 'center'}} start={{x: 2, y: 0.15}}>
                                  <Image style={styles.profilepic} source={require('../images/nopic.png')} />
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
                          
                          <TouchableOpacity onPress={() => navigation.navigate('Configuracao')} style={{width: 170, height: 45, alignSelf: 'center', 
                          marginTop: 20, borderRadius: 19, alignItems: 'center',justifyContent: 'center',}}>
                            <LinearGradient colors={['#ec230d', '#f75738', '#be6049']} style={{width: 170, height: 45, alignSelf: 'center', 
                          marginTop: 20, borderRadius: 19, alignItems: 'center',justifyContent: 'center',}} start={{x: 2, y: 0.15}}>
                           
                              <Text style={{fontSize: 15, fontWeight: 'bold', color: 'white'}}>Editar</Text>
                              </LinearGradient>
                            </TouchableOpacity>
                           

                      </View>
                      {
                          userdata.posts.length > 0 ?
                              <View style={styles.c50}>
                                  <Text style={{
                                       color: 'black',
                                       fontSize: 20,
                                       fontWeight: 'bold',
                                       marginTop: 20

                                  }}>Posts</Text>
                                  <View style={{width: 80, height: 2, backgroundColor: '#ec230d'}}></View>
                                  <View style={styles.c13}>
                                      {
                                          userdata.posts?.map(
                                              (item) => {
                                                  return (
                                                      <Image key={item.post} style={styles.postpic}
                                                          source={{ uri: item.post }}
                                                      />
                                                  )
                                              }
                                          )
                                      }
                                  </View>
                              </View>
                              :
                              <View style={styles.c2}>
                              <Text style={styles.txt1}>Você não tem nenhuma publicação </Text>
                          </View>
                      }

                  </ScrollView>

                  :
                  <ActivityIndicator size="large" color="white" />
          }

      </View>
  )
}

export default Perfil

const styles = StyleSheet.create({

  container: {
    width: '100%',
    height: '100%',
    backgroundColor: '#FAFAFA',
},
c1: {
    width: '100%',
    height: 400,
    borderBottomLeftRadius: 30, borderBottomRightRadius: 30,
    alignItems: 'center',
    backgroundColor: 'white', elevation: 10
},
c50: {
  width: '100%',
  alignItems: 'center',
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
    backgroundColor: 'black'
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
}

})