import { StyleSheet, Text, View, ActivityIndicator, Image } from 'react-native'
import React from 'react'
import { TextInput } from 'react-native-gesture-handler'
import { TouchableOpacity } from 'react-native'
import AsyncStorage from '@react-native-async-storage/async-storage';
import { LinearGradient } from 'expo-linear-gradient';

const Login = ({navigation}) => {
  const [email, setEmail] = React.useState('')
    const [senha, setSenha] = React.useState('')
    const [loading, setLoading] = React.useState(false)

    const handleLogin = () => {
        if (email == '' || senha == '') {
            alert('Por favor entre com o email e a senha')
        }
        else {
            setLoading(true)
            fetch('http://192.168.0.54:3000/login', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    email: email,
                    senha: senha
                })
            })
                .then(res => res.json())
                .then(async data => {
                    if (data.error) {
                        setLoading(false)
                       
                    }
                    else if (data.message == 'Sucessoaofazerlogin') {
                        setLoading(false)
                        await AsyncStorage.setItem('user', JSON.stringify(data))
                        navigation.navigate('Menu', { data })
                    }
                })
                .catch(err => {
                    setLoading(false)
                   
                })
        }
    }
  return (
    <View style={{width: '100%', height: '100%', justifyContent: 'center', alignItems: 'center'}}>
      <Image source={require('../images/balvintext.png')} style={{width: 250, height: 150, marginTop: 125}}/>

      <TextInput placeholder='Número de telefone, email ou nome de usuário' style={{fontSize: 12,height: 50, width: '90%', backgroundColor: 'white', padding: 10, borderRadius: 5, elevation: 10, marginTop: 40}} onChangeText={(text) => setEmail(text)}/>
      <TextInput placeholder='Senha' style={{height: 50, width: '90%', backgroundColor: 'white', padding: 10, borderRadius: 5, elevation: 5, marginTop: 20,fontSize: 12}} secureTextEntry={true}
                onChangeText={(text) => setSenha(text)}/>
      {
                loading ?
                    <ActivityIndicator size="large" color="white"  style={{marginTop:20}}/>
                    :

      <TouchableOpacity onPress={() => handleLogin()} style={{width: '90%', height: 45, justifyContent: 'center', alignItems: 'center', borderRadius: 5, marginTop: 20}}>
       <LinearGradient colors={['#ec230d', '#f75738', '#be6049']} start={{x: 2, y: 0.15}} style={{width: '100%', height: 45, justifyContent: 'center', alignItems: 'center', borderRadius: 5}}>
        <Text style={{color: 'white', fontSize: 15, fontWeight: 'bold'}}>Entrar</Text>
        </LinearGradient>
      </TouchableOpacity>

      }

<Text style={{marginTop: 15, fontSize: 12, textAlign: 'center', color: 'gray'}}>Esqueceu seus dados de login?<Text style={{
  color: '#ec230d', fontWeight: 'bold'
}} onPress={() => navigation.navigate('Esqueceuasenhaemail')}> Clique aqui.</Text></Text>

      <View style={{width: '100%', height: 40, borderWidth: 0.5, borderColor: 'gray', marginTop:185,
       alignItems: 'center', justifyContent: 'center'}}>

      <Text style={{fontSize: 10, fontStyle: 'italic', color: 'gray' }}>Não tem uma conta?
        <Text style={{fontSize: 10, fontStyle: 'italic', color: '#ec230d', fontWeight: 'bold'}} onPress={() => navigation.navigate('Registraremail')}> Cadastre-se</Text>
      </Text>
    

      </View>
    </View>
  )
}

export default Login

const styles = StyleSheet.create({})